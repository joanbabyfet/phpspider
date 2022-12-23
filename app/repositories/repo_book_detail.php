<?php


namespace App\repositories;


use App\Models\mod_book_detail;
use App\services\serv_util;
use App\traits\trait_repo_base;
use Illuminate\Support\Facades\Storage;

class repo_book_detail
{
    use trait_repo_base;

    private $model;   //需要定义为私有变量
    public $page_size = 20; //每页展示几笔

    public function __construct(mod_book_detail $mod_book_detail)
    {
        $this->model = $mod_book_detail;
    }

    /**
     * 获取列表
     * @param array $conds
     * @return array
     */
    public function get_list(array $conds)
    {
        $page_size  = !empty($conds['page_size']) ? $conds['page_size'] : $this->page_size;
        $order_by   = !empty($conds['order_by']) ? $conds['order_by'] : ['create_time', 'desc']; //默认添加时间正序
        $group_by   = !empty($conds['group_by']) ? $conds['group_by'] : []; //分组
        $name      = !empty($conds['title']) ? $conds['title'] : '';
        $pid       = !empty($conds['pid']) ? $conds['pid'] : '';
        $id         = !empty($conds['id']) ? $conds['id'] : [];

        $where = []; //筛选
        $name and $where[] = ['title', 'like', "%{$name}%"];
        $pid and $where[] = ['pid', '=', $pid];
        $id and $where[] = ['id', $id];

        $rows = $this->lists([
            'fields'        => $conds['fields'] ?? null,
            'where'         => $where,
            'page'          => $conds['page'] ?? null,
            'page_size'     => $page_size,
            'order_by'      => $order_by,
            'group_by'      => $group_by,
            'count'         => $conds['count'] ?? null, //是否显示总条数
            'limit'         => $conds['limit'] ?? null,
            'field'         => $conds['field'] ?? null,
            'append'        => $conds['append'] ?? null, //展示扩充字段(默认展示) []=不展示
            'lock'          => $conds['lock'] ?? null, //排他鎖
            'share'         => $conds['share'] ?? null, //共享鎖
            'load'          => $conds['load'] ?? null, //加载外表
            'index'         => $conds['index'] ?? null,
            'with_count'    => $conds['with_count'] ?? null,
        ])->toArray();
        return $rows;
    }

    /**
     * 添加或修改
     * @param array $data
     * @return int|mixed
     * @throws \Throwable
     */
    public function save(array $data, &$ret_data = [])
    {
        $do             = isset($data['do']) ? $data['do'] : '';
        //参数过滤
        $data_filter = data_filter([
            'do'            => 'required',
            'id'            => $do == 'edit' ? 'required' : '',
            'pid'           => '',
            'chapter_id'    => '',
            'title'         => 'required',
            'from_url'    => '',
            'from_hash'   => '',
        ], $data);

        $status = 1;
        try
        {
            if(!is_array($data_filter))
            {
                $this->exception(trans('api.api_param_error'), -1);
            }

            $id = $data_filter['id'] ?? '';
            unset($data_filter['do'], $data_filter['id']);

            if($do == 'add')
            {
                $data_filter['create_time'] = time();
                $id = $this->insert($data_filter);
                $ret_data['id'] = $id;
            }
            elseif($do == 'edit')
            {
                $data_filter['update_time'] = time();
                $this->update($data_filter, ['id' => $id]);
            }
        }
        catch (\Exception $e)
        {
            $status = $this->get_exception_status($e);
            //记录日志
            logger(__METHOD__, [
                'status'  => $status,
                'errcode' => $e->getCode(),
                'errmsg'  => $e->getMessage(),
                'data'    => $data,
            ]);
        }
        return $status;
    }

    /**
     * 删除
     * @param array $data
     * @return int|mixed
     * @throws \Throwable
     */
    public function del(array $data)
    {
        //参数过滤
        $data_filter = data_filter([
            'id'            => 'required',
        ], $data);

        $status = 1;
        try
        {
            if(!is_array($data_filter))
            {
                $this->exception(trans('api.api_param_error'), -1);
            }

            $id = $data_filter['id'] ?? '';
            unset($data_filter['id']);

            $data_filter['delete_time'] = time();
            $data_filter['delete_user'] = defined('AUTH_UID') ? AUTH_UID : '';
            $this->update($data_filter, ['id' => $id]);
        }
        catch (\Exception $e)
        {
            $status = $this->get_exception_status($e);
            //记录日志
            logger(__METHOD__, [
                'status'  => $status,
                'errcode' => $e->getCode(),
                'errmsg'  => $e->getMessage(),
                'data'    => $data,
            ]);
        }
        return $status;
    }

    /**
     * 将章节内容保存到本地txt文件, 独立事件
     * @param $pid
     * @param $id
     * @param $content
     * @return bool
     */
    public function set_content($pid, $id, $content)
    {
        $upload_dir = "public/content/{$pid}/"; //这里不能用绝对路径
        //檢測目錄是否存在,不存在則創建
        app(serv_util::class)->path_exists($upload_dir);
        //保存到本地
        return Storage::disk('local')->put($upload_dir."{$id}.txt", $content);
    }

    /**
     * 获取小说章节内容
     * @param $pid 小说id
     * @param $id 小说章节id
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function get_content($pid, $id)
    {
        if(empty($pid) || empty($id)) {
            return '';
        }

        $upload_dir = "public/content/{$pid}/"; //这里不能用绝对路径
        $content = Storage::disk('local')->get($upload_dir."{$id}.txt");
        return $content;
    }

    /**
     * 获取某小说最新章节信息
     * @param $pid 小說id
     * @return array|mixed
     */
    public function get_last_zhangjie($pid)
    {
        $row = $this->find([
            'fields'    => ['id', 'chapter_id', 'title', 'from_hash'],
            'where'     => [
                ['pid', '=', $pid]
            ],
            'order_by'  => ['chapter_id', 'desc'],
        ]);
        $row = empty($row) ? []:$row->toArray();
        return $row;
    }
}
