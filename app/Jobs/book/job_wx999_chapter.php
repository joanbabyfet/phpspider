<?php

namespace App\Jobs\book;

use App\lib\response;
use App\repositories\repo_book;
use App\repositories\repo_book_detail;
use App\services\serv_util;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use QL\QueryList;

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
    private $source;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($book , $count = 0, $source = '')
    {
        $this->book = $book;
        $this->count = $count; //章节数量
        $this->source = $source; //采集来源
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        repo_book $repo_book,
        repo_book_detail $repo_book_detail,
        serv_util $serv_util
    )
    {
        $config_book            = config('book.'.$this->source);
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
                //获取该页面html源码
                $html = file_get_contents($this->book['from_url']);
                $info = QueryList::Query($html , $detail_rules['rules'], '')->getData(function($item){
                    return $item;
                });

                $update_data = [];
                if(empty($this->book['introduce']) && !empty($info[0]['introduce'])){
                    $update_data['introduce'] = $info[0]['introduce'];
                }

                if(empty($this->book['thumb']) && !empty($info[0]['thumb']))
                {
                    //将远程图片下载至本地
                    $thumb = $serv_util->get_remote_image($info[0]['thumb']);
                    if($thumb){
                        if(config('app.debug') == false){
                            //TODO 上传到云存储 例s3
                            $update_data['thumb'] = $thumb;
                        }else{
                            $update_data['thumb'] = $thumb;
                        }
                    }
                }
            }

            //获取章节列表
            $html = file_get_contents($this->book['from_url']);
            $zhangjie_lists = QueryList::Query($html , $zhangjie_list_rules['rules'] , $zhangjie_list_rules['range'])->getData(function($item){
                return $item;
            });

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
                    $job_content = new job_wx999_content($v, $this->source);
                    dispatch($job_content->onQueue('collect'));
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
