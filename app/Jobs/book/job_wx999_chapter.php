<?php

namespace App\Jobs\book;

use App\repositories\repo_book;
use App\repositories\repo_book_detail;
use App\services\serv_upload;
use App\services\serv_util;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

/**
 * 1.将远程图片下载至本地
 * 2.更新小说信息, 包含简介/缩图
 * 3.推送任务到队列(采集每章节内容), 1个章节1个任务
 * Class job_wx999_chapter
 * @package App\Jobs\book
 */
class job_wx999_chapter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $book;
    private $count;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($book , $count = 0)
    {
        $this->book = $book;
        $this->count = $count; //章节数量
    }

    /**
     * Execute the job.
     *
     * @param repo_book $repo_book
     * @param repo_book_detail $repo_book_detail
     * @param serv_util $serv_util
     * @param serv_upload $serv_upload
     * @return bool
     * @throws \Throwable
     */
    public function handle(
        repo_book $repo_book,
        repo_book_detail $repo_book_detail,
        serv_util $serv_util,
        serv_upload $serv_upload
    )
    {
        $source                 = $this->book['source'] ?? ''; //来源网站
        $config_book            = config('book.'.$source);
        $base_url               = $config_book['base_url'];
        $detail_rules           = $config_book['detail']; //详情页配置
        $zhangjie_list_rules    = $config_book['zhangjie_list']; //章节列表页配置

        if(empty($this->book['from_url']) || empty($this->book['id']))
        {
            //写入日志
            logger(__METHOD__, [
                'status'    => -1,
                'errmsg'    => '采集失败',
                'data'      => $this->book,
            ]);
            return true;
        }

        try
        {
            //更新文章详情与缩略图
            if(empty($this->book['introduce']) || empty($this->book['thumb']) )
            {
                //采集数据
                $info = $serv_util->collect([
                    'url'   => $this->book['from_url'],
                    'rules' => $detail_rules['rules'],
                    'range' => '',
                ]);

                $update_data = [];
                if(empty($this->book['introduce']) && !empty($info[0]['introduce'])){
                    $update_data['introduce'] = $info[0]['introduce'];
                }

                if(empty($this->book['thumb']) && !empty($info[0]['thumb']))
                {
                    //将远程图片下载至本地
                    $thumb = $serv_util->get_remote_image($info[0]['thumb']);
                    if($thumb)
                    {
                        $s3_thumb = false;
                        if(config('app.debug') == false)
                        {
                            //上传到s3
                            $s3_thumb = $serv_upload->upload2s3($thumb);
                        }

                        if($s3_thumb)
                        {
                            $update_data['thumb'] = $thumb;
                            //干掉本地图片
                            $upload_dir = 'public/image/'; //这里不能用绝对路径
                            Storage::disk('local')->delete($upload_dir.$thumb);
                        }
                        else
                        {
                            $update_data['thumb'] = $thumb;
                        }
                    }
                }
            }

            //获取章节列表
            $zhangjie_lists = $serv_util->collect([
                'url'   => $this->book['from_url'],
                'rules' => $zhangjie_list_rules['rules'],
                'range' => $zhangjie_list_rules['range'],
            ]);

            if(count($zhangjie_lists))
            {
                foreach($zhangjie_lists as $k => &$v)
                {
                    $v = array_map('trim',$v); //移除所有字段空格

                    if(empty($v['from_url']))
                    {
                        unset($zhangjie_lists[$k]);
                    }
                    else
                    {
                        //组装章节列表页完整链接
                        if (substr($v['from_url'], 0, 4) !== 'http') {
                            $v['from_url'] = $base_url.substr($v['from_url'], 1);
                        }
                    }
                }

                //获取某小说最后一篇章节
                $last_article = $repo_book_detail->get_last_zhangjie($this->book['id']);
                //获取章节序号
                $first_chapter_id = !empty($last_article) ? $last_article['chapter_id'] + 1 : 1;
                if(!empty($last_article))
                {
                    $links = $this->count ? array_slice($zhangjie_lists, $first_chapter_id - 1, $this->count) :
                        array_slice($zhangjie_lists, $first_chapter_id - 1);
                }
                else
                {
                    $links = $this->count ? array_slice($zhangjie_lists, 0, $this->count) :
                        array_slice($zhangjie_lists, $first_chapter_id - 1);
                }
                $zhangjie = end($links); //取所有章节数组中最后一条

                if(is_array($zhangjie) && isset($zhangjie['title'])){
                    $update_data['zhangjie'] = $zhangjie['title'];
                }

                //更新小说信息
                $data = array_merge($update_data, [
                    'do'    => 'edit',
                    'id'    => $this->book['id'],
                ]);
                $repo_book->save($data);

                //章节列表
                foreach($links as $v)
                {
                    //每条章节信息
                    $v = array_merge($v, [
                        'chapter_id'    => $first_chapter_id++, //章节序号
                        'pid'           => $this->book['id'] //小说id
                    ]);
                    //推送任务到队列(采集每章节内容)
                    $class_name = '\App\Jobs\book\\'.'job_'.$source.'_content';
                    dispatch(new $class_name($v, $source))->onQueue('collect');
                }
                unset($html, $zhangjie_lists, $links); //释放大变量内存
            }
        }
        catch(\Exception $e)
        {
            //写入日志
            logger(__METHOD__, [
                'status'  => -2,
                'errcode' => $e->getCode(),
                'errmsg'  => $e->getMessage(),
            ]);
        }
    }
}
