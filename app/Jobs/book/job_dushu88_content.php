<?php

namespace App\Jobs\book;

use App\Models\mod_book_detail;
use App\repositories\repo_book_content;
use App\repositories\repo_book_detail;
use App\services\serv_util;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class job_dushu88_content implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $info;
    private $source;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($info, $source = '')
    {
        $this->info     = $info;
        $this->source   = $source;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        repo_book_detail $repo_book_detail,
        repo_book_content $repo_book_content,
        serv_util $serv_util
    )
    {
        $config_book            = config('book.'.$this->source);
        $zhangjie_detail_rules  = $config_book['zhangjie_detail']; //章节详情页配置

        //采集数据
        $res = $serv_util->collect([
            'url'   => $this->info['from_url'],
            'rules' => $zhangjie_detail_rules['rules'],
            'range' => $zhangjie_detail_rules['range'],
        ]);
        $res = array_shift($res); //将第1个数组元素弹出
        $content = empty($res) ? '' : $res['content'];

        DB::beginTransaction(); //开启事务, 保持数据一致
        try {
            $insert_data = [
                'do'            => 'add',
                'pid'           => $this->info['pid'],
                'chapter_id'    => $this->info['chapter_id'],
                'title'         => $this->info['title'],
                'hit'           => 0,
                'from_url'      => $this->info['from_url'],
                'from_hash'     => md5($this->info['from_url']),
                'status'        => mod_book_detail::ENABLE,
            ];
            //每条小说章节入库
            $repo_book_detail->save($insert_data, $ret_data);
            //章节内容入库
            $insert_content = [
                'do'            => 'add',
                'zhangjie_id'   => $ret_data['id'],
                'content'       => $content,
            ];
            $repo_book_content->save($insert_content);
            //保存章节内容到txt, 内容干掉php与html标签
            $content and $repo_book_detail->set_content($this->info['pid'], $ret_data['id'], strip_tags($content));

            DB::commit(); //手動提交事务

            //写入日志
            logger(__METHOD__, [
                'status'        => 1,
                'msg'           => 'success',
                'zhangjie_id'   => $ret_data['id'],
            ]);
        }
        catch (\Exception $e)
        {
            DB::rollBack(); //手動回滚事务
            //写入日志
            logger(__METHOD__, [
                'status'  => -3,
                'errcode' => $e->getCode(),
                'errmsg'  => $e->getMessage(),
            ]);
        }

        unset($html, $match, $content); //释放大变量内存
    }
}
