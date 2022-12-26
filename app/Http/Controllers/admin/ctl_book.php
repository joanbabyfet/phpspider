<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\mod_book;
use App\repositories\repo_book;
use App\repositories\repo_book_content;
use App\repositories\repo_book_detail;
use App\repositories\repo_category;
use App\services\serv_array;
use App\services\serv_util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ctl_book extends Controller
{
    private $repo_category;
    private $repo_book;
    private $repo_book_detail;
    private $repo_book_content;
    private $serv_array;
    private $serv_util;

    public function __construct(
        repo_category $repo_category,
        repo_book $repo_book,
        repo_book_detail $repo_book_detail,
        repo_book_content $repo_book_content,
        serv_array $serv_array,
        serv_util $serv_util
    )
    {
        parent::__construct();
        $this->repo_category    = $repo_category;
        $this->repo_book        = $repo_book;
        $this->repo_book_detail = $repo_book_detail;
        $this->repo_book_content = $repo_book_content;
        $this->serv_array       = $serv_array;
        $this->serv_util        = $serv_util;
    }

    /**
     * 获取小说列表
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $title      = $request->input('title','');
        $page_size  = $request->input('page_size', $this->repo_book->page_size);
        $page       = $request->input('page', 1);

        $conds = [
            'title'         => $title,
            'page_size'     => $page_size, //每页几条
            'page'          => $page, //第几页
            'count'         => 1, //是否返回总条数
        ];
        $rows = $this->repo_book->get_list($conds);
        return res_success($rows);
    }

    /**
     * 删除
     * @version 1.0.0
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $id = $request->input('id'); //小说id

        $status = $this->repo_book->del(['id' => $id]);
        if($status < 0)
        {
            return res_error($this->repo_book->get_err_msg($status), $status);
        }
        return res_success([], trans('api.api_delete_success'));
    }

    /**
     * 获取小说章节内容
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function get_content(Request $request)
    {
        $id = $request->input('id'); //章节id
        $pid = $request->input('pid'); //小说id

        if(empty($id) || empty($pid))
        {
            return res_error(trans('api.api_param_error'), -1);
        }

        $content = $this->repo_book_detail->get_content($pid, $id);
        return res_success(['content' => $content]);
    }

    /**
     * 获取小说章节列表
     * @param Request $request
     * @return mixed
     */
    public function get_chapter_list(Request $request)
    {
        $id         = $request->input('id'); //小说id
        $title      = $request->input('title','');
        $page_size  = $request->input('page_size', $this->repo_book_detail->page_size);
        $page       = $request->input('page', 1);

        $conds = [
            'pid'           => $id,
            'title'         => $title,
            'page_size'     => $page_size, //每页几条
            'page'          => $page, //第几页
            'order_by'      => ['chapter_id', 'desc'],
            'count'         => 1, //是否返回总条数
        ];
        $rows = $this->repo_book_detail->get_list($conds);
        return res_success($rows);
    }

    /**
     * 获取小说分类
     * @param Request $request
     * @return mixed
     */
    public function get_category_options(Request $request)
    {
        $page_size  = $request->input('page_size', $this->repo_category->page_size);
        $page       = $request->input('page', 1);

        $conds = [
            'page_size'     => $page_size, //每页几条
            'page'          => $page, //第几页
        ];

        $rows = $this->repo_category->get_list($conds);
        $options = $this->serv_array->one_array($rows, ['id', 'name']);
        return res_success($options);
    }

    /**
     * 获取当前队列数量
     * @return int
     */
    public function get_queue_number()
    {
        if (config('queue.default') == 'database') {
            $number = DB::table('jobs')->count();
        }
        else {
            $number = 0;
        }
        return res_success(['number' => $number]);
    }

    /**
     * 获取采集来源
     * @param Request $request
     * @return mixed
     */
    public function get_source_options(Request $request)
    {
        $source = config('book.source');
        return res_success(['lists' => $source]);
    }

    /**
     * 添加采集队列
     * @version 1.0.0
     * @param Request $request
     * @return mixed
     */
    public function add_queue(Request $request)
    {
        $data   = $request->all();
        $source = $data['source'];
        $source_list = array_keys(config('book.source'));

        if(!in_array($source, $source_list) || empty($source))
        {
            return res_error(trans('api.api_param_error'), -1);
        }

        return $this->$source($data);
    }

    /**
     * 更新采集队列
     * @version 1.0.0
     * @param Request $request
     * @return mixed
     */
    public function update_queue(Request $request)
    {
        $type               = $request->input('type'); //更新类型
        $cat_id             = $request->input('cat_id', []); //分类id, 数组
        $start_id           = $request->input('start_id'); //起始小说id
        $end_id             = $request->has('end_id') ?
            $request->input('end_id') : $start_id + 100; //结束小说id
        $target_id          = $request->input('target_id'); //指定小说id
        $number             = $request->input('number', 0);
        $number             = $number < 1 ? 10 : $number; //小说数量
        $zhangjie_number    = $request->input('zhangjie_number', 0); //章节数量
        $zhangjie_number    = $zhangjie_number < 1 ? 50 : $zhangjie_number;

        if($type == 1) { //指定分类
            $conds = [
                'limit'     => $number, //取几篇小说
                'cat_id'    => $cat_id,
                'order_by'  => ['update_time', 'asc'],
            ];
            $rows = $this->repo_book->get_list($conds);
        }
        elseif($type == 2) //指定小说id范围
        {
            $conds = [
                'start_id'  => $start_id,
                'end_id'    => $end_id
            ];
            $rows = $this->repo_book->get_list($conds);
        }
        elseif($type == 3) //指定文章
        {
            $conds = [
                'id'    => $target_id, //某小说id
            ];
            $rows = $this->repo_book->get_list($conds);
        }
        elseif($type == 4) //修复空白数据
        {
            $conds = [
                'fields'    => ['zhangjie_id'],
                'is_empty'  => 1,
                'limit'     => $zhangjie_number, //取几个章节
                'order_by'  => ['zhangjie_id', 'asc'],
            ];
            $rows = $this->repo_book_content->get_list($conds);
            $zhangjie_ids = sql_in($rows, 'zhangjie_id');

            //获取小说章节列表
            $zhangjie_list = $this->repo_book_detail->get_list(['id'  => $zhangjie_ids]);
            $book_ids = sql_in($zhangjie_list, 'pid');

            //获取小说来源 source
            $books = $this->repo_book->get_list(['id'  => $book_ids]);
            $book_source = one_array($books, ['id', 'source']);

            $success_zhangjie_count = 0; //成功章节内容入库条数
            foreach ($zhangjie_list as $v)
            {
                $config_book            = config('book.'.$book_source[$v['pid']]); //根据小说id获取采集配置
                $zhangjie_detail_rules  = $config_book['zhangjie_detail']; //列表页配置

                //采集数据
                $res = $this->serv_util->collect([
                    'url'   => $v['from_url'],
                    'rules' => $zhangjie_detail_rules['rules'],
                    'range' => $zhangjie_detail_rules['range'],
                ]);
                $res = array_shift($res); //将第1个数组元素弹出

                if(!empty($res['content']))
                {
                    //更新章节内容
                    $update_data = [
                        'do'            => 'edit',
                        'zhangjie_id'   => $v['id'], //章节id
                        'content'       => $res['content'],
                    ];
                    $this->repo_book_content->save($update_data);
                    $success_zhangjie_count++;
                }
            }

            //统计信息
            $zhangjie_total = count($zhangjie_list); //章节总条数
            $success_percent = sprintf('%.2f', $success_zhangjie_count / $zhangjie_total) * 100;

            return res_success([
                'success_zhangjie_count'    => $success_zhangjie_count, //更新了几条章节
                'success_percent'           => $success_percent //恢复章节成功率
            ]);
        }

        //采集来源配置
        $success_count = 0;
        $source_list = array_keys(config('book.source'));
        foreach ($rows as $v)
        {
            if (in_array($v['source'], $source_list))
            {
                //ucfirst函数为第1个字元为字母则转大写并返回字符串
                $class_name = '\App\Jobs\book\\'.'job_'.$v['source'].'_chapter';
                dispatch(new $class_name($v, $zhangjie_number, $v['source']))->onQueue('collect');
                $success_count++;
            }
        }

        return res_success([
            'success_count'  => $success_count, //成功更新了几篇小說
        ]);
    }

    /**
     * 999小说, 每个网站采集规则不可, 独立事件
     * 1.读取待采集栏目页面所有指定链接
     * 2.对链接进行补全，得到完整链接
     * 3.将该链接放入数据库中查询,判断是否存在记录
     * @param $data
     * @throws \Throwable
     */
    public function wx999($data)
    {
        $config_book        = config('book.'.__FUNCTION__); //获取采集配置
        $base_url           = $config_book['base_url'];
        $charset            = $config_book['charset'];//编码
        $category_map       = $config_book['category'];//该站分类对应
        $list_rules         = $config_book['list']; //列表页配置
        $number             = $data['number'] ?? 10;
        $number             = $number < 1 ? 10 : $number;
        $zhangjie_number    = $data['zhangjie_number'] ?? 0; //获取章节数量

        $cat_ids            = [];
        if (empty($data['cat_id'])){ //获取小说分类
            $rows = $this->repo_category->get_list([
                'pid'       => '0',
            ]);
            $cat_ids = $this->serv_array->sql_in($rows, 'id');
        }
        else {
            $cat_ids = $data['cat_id'];
        }

        $success_count = 0; //成功入库条数
        foreach ($cat_ids as $cat_id)
        {
            $collect_cat = $category_map[$cat_id];
            $total_page = ceil($number / $list_rules['page_size']);//需要采集的总页数

            $cat_count = 0;
            for ($page = 1; $page <= $total_page; $page++)
            {
                $url = $base_url.sprintf($list_rules['page_url'], $collect_cat, $page); //组装第几页地址
                //采集数据
                $res = $this->serv_util->collect([
                    'url'   => $url,
                    'rules' => $list_rules['rules'],
                    'range' => $list_rules['range'],
                ]);

                //过滤标题与链接为空的无效数据
                $result = array_filter($res, function($v) {
                    if (!empty($v['title']) && !empty($v['from_url'])){
                        return true;
                    }
                });

                if ($page == $total_page) {
                    $result = array_slice($result, 0, $number - $cat_count);//最后一页截取指定剩余数量
                }

                //小说列表
                foreach($result as &$v)
                {
                    $v = array_map('trim', $v); //移除所有字段空格

                    if ($cat_count >= $number){ //当前分类已采集完毕
                        break;
                    }

                    //组装列表页完整链接
                    if (substr($v['from_url'], 0, 4) !== 'http') {
                        $v['from_url'] = $base_url.substr($v['from_url'], 1);
                    }

                    $row = $this->repo_book->find(['where' => [['from_hash', '=', md5($v['from_url'])]]]);
                    if($row)
                    {
                        $v = $row->toArray(); //推送任务到队列
                    }
                    else
                    {
                        //来源地址hash不在存则入库
                        $v = [
                            'do'            => 'add',
                            'cat_id'        => $cat_id,
                            'title'         => $v['title'],
                            'introduce'     => $v['introduce'] ?? '',
                            'thumb'         => $v['thumb'] ?? '',
                            'zhangjie'      => $v['zhangjie'] ?? '', //最新章节
                            'author'        => $v['author'] ?? '',
                            'word_count'    => 0,
                            'follow'        => 0,
                            'hit'           => 0,
                            'status'        => mod_book::ENABLE,
                            'source'        => __FUNCTION__,
                            'from_url'      => $v['from_url'],
                            'from_hash'     => md5($v['from_url']),
                        ];
                        $this->repo_book->save($v, $ret_data);
                        $v['id'] = $ret_data['id'];
                        unset($v['do']); //干掉不需要字段
                    }

                    //推送任务到队列, 1个小说1个任务
                    $class_name = '\App\Jobs\book\\'.'job_'.__FUNCTION__.'_chapter';
                    dispatch(new $class_name($v, $zhangjie_number))->onQueue('collect');

                    $cat_count++;
                    $success_count++;
                }
            }
        }
    }

    /**
     * 88读书网, 每个网站采集规则不可, 独立事件
     * 1.读取待采集栏目页面所有指定链接
     * 2.对链接进行补全，得到完整链接
     * 3.将该链接放入数据库中查询,判断是否存在记录
     * @param $data
     * @throws \Throwable
     */
    public function dushu88($data)
    {
        $config_book        = config('book.'.__FUNCTION__); //获取采集配置
        $base_url           = $config_book['base_url'];
        $charset            = $config_book['charset'];//编码
        $category_map       = $config_book['category'];//该站分类对应
        $list_rules         = $config_book['list']; //列表页配置
        $number             = $data['number'] ?? 10;
        $number             = $number < 1 ? 10 : $number;
        $zhangjie_number    = $data['zhangjie_number'] ?? 0; //获取章节数量

        $cat_ids            = [];
        if (empty($data['cat_id'])){ //获取小说分类
            $rows = $this->repo_category->get_list([
                'pid'       => '0',
            ]);
            $cat_ids = $this->serv_array->sql_in($rows, 'id');
        }
        else {
            $cat_ids = $data['cat_id'];
        }

        $success_count = 0; //成功入库条数
        foreach ($cat_ids as $cat_id)
        {
            $collect_cat = $category_map[$cat_id];
            $total_page = ceil($number / $list_rules['page_size']);//需要采集的总页数

            $cat_count = 0;
            for ($page = 1; $page <= $total_page; $page++)
            {
                $url = $base_url.sprintf($list_rules['page_url'], $collect_cat, $page); //组装第几页地址
                //采集数据
                $res = $this->serv_util->collect([
                    'url'   => $url,
                    'rules' => $list_rules['rules'],
                    'range' => $list_rules['range'],
                ]);

                //过滤标题与链接为空的无效数据
                $result = array_filter($res, function($v) {
                    if (!empty($v['title']) && !empty($v['from_url'])){
                        return true;
                    }
                });

                if ($page == $total_page) {
                    $result = array_slice($result, 0, $number - $cat_count);//最后一页截取指定剩余数量
                }

                //小说列表
                foreach($result as &$v)
                {
                    $v = array_map('trim', $v); //移除所有字段空格

                    if ($cat_count >= $number){ //当前分类已采集完毕
                        break;
                    }

                    //组装列表页完整链接
                    if (substr($v['from_url'], 0, 4) !== 'http') {
                        $v['from_url'] = $base_url.substr($v['from_url'], 1);
                    }

                    $row = $this->repo_book->find(['where' => [['from_hash', '=', md5($v['from_url'])]]]);
                    if($row)
                    {
                        $v = $row->toArray(); //推送任务到队列
                    }
                    else
                    {
                        //来源地址hash不在存则入库
                        $v = [
                            'do'            => 'add',
                            'cat_id'        => $cat_id,
                            'title'         => $v['title'],
                            'introduce'     => $v['introduce'] ?? '',
                            'thumb'         => $v['thumb'] ?? '',
                            'zhangjie'      => $v['zhangjie'] ?? '', //最新章节
                            'author'        => $v['author'] ?? '',
                            'word_count'    => 0,
                            'follow'        => 0,
                            'hit'           => 0,
                            'status'        => mod_book::ENABLE,
                            'source'        => __FUNCTION__,
                            'from_url'      => $v['from_url'],
                            'from_hash'     => md5($v['from_url']),
                        ];
                        $this->repo_book->save($v, $ret_data);
                        $v['id'] = $ret_data['id'];
                        unset($v['do']); //干掉不需要字段
                    }

                    //推送任务到队列, 1个小说1个任务
                    $class_name = '\App\Jobs\book\\'.'job_'.__FUNCTION__.'_chapter';
                    dispatch(new $class_name($v, $zhangjie_number))->onQueue('collect');

                    $cat_count++;
                    $success_count++;
                }
            }
        }
    }
}
