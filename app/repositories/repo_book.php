<?php


namespace App\repositories;


use App\Models\mod_book;
use App\traits\trait_repo_base;

class repo_book
{
    use trait_repo_base;

    private $model;   //需要定义为私有变量
    public $page_size = 20; //每页展示几笔

    public function __construct(mod_book $mod_book)
    {
        $this->model = $mod_book;
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
        $name       = !empty($conds['title']) ? $conds['title'] : '';
        $cat_id     = !empty($conds['cat_id']) ? $conds['cat_id'] : [];
        $start_id   = !empty($conds['start_id']) ? $conds['start_id'] : '';
        $end_id     = !empty($conds['end_id']) ? $conds['end_id'] : '';
        $id         = !empty($conds['id']) ? $conds['id'] : [];

        $where = []; //筛选
        $name and $where[] = ['title', 'like', "%{$name}%"];
        $cat_id and $where[] = ['cat_id', $cat_id];
        $id and $where[] = ['id', $id];
        $start_id and $where[] = ['id', '>=', (int)$start_id]; //起始id
        $end_id and $where[] = ['id', '<=', (int)$end_id]; //结束id

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
            'cat_id'        => '',
            'title'         => $do == 'edit' ? '' : 'required',
            'introduce'     => '',
            'thumb'         => '',
            'zhangjie'      => '',
            'author'        => '',
            'word_count'    => '',
            'level'         => '',
            'source'        => '',
            'from_url'      => '',
            'from_hash'     => '',
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

            $this->delete(['id' => $id]); //直接干掉, 不做软删除
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
}
