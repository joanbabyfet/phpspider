<?php


namespace App\repositories;


use App\Models\mod_book_content;
use App\traits\trait_repo_base;

class repo_book_content
{
    use trait_repo_base;

    private $model;   //需要定义为私有变量
    public $page_size = 20; //每页展示几笔

    public function __construct(mod_book_content $mod_book_content)
    {
        $this->model = $mod_book_content;
    }

    /**
     * 获取列表
     * @param array $conds
     * @return array
     */
    public function get_list(array $conds)
    {
        $page_size  = !empty($conds['page_size']) ? $conds['page_size'] : $this->page_size;
        $order_by   = !empty($conds['order_by']) ? $conds['order_by'] : ['zhangjie_id', 'asc']; //默认添加时间正序
        $group_by   = !empty($conds['group_by']) ? $conds['group_by'] : []; //分组
        $is_empty   = empty($conds['is_empty']) ? 0 : $conds['is_empty'];

        $where = []; //筛选
        $is_empty and $where[] = ['content', '=', ''];

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
            'zhangjie_id'   => 'required',
            'content'       => '',
        ], $data);

        $status = 1;
        try
        {
            if(!is_array($data_filter))
            {
                $this->exception(trans('api.api_param_error'), -1);
            }

            $id = $data_filter['zhangjie_id'] ?? '';
            unset($data_filter['do']);

            if($do == 'add')
            {
                $id = $this->insert($data_filter);
            }
            elseif($do == 'edit')
            {
                unset($data_filter['zhangjie_id']);
                $this->update($data_filter, ['zhangjie_id' => $id]);
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
            'zhangjie_id'   => 'required',
        ], $data);

        $status = 1;
        try
        {
            if(!is_array($data_filter))
            {
                $this->exception(trans('api.api_param_error'), -1);
            }

            $id = $data_filter['zhangjie_id'] ?? '';
            unset($data_filter['zhangjie_id']);

            $this->delete(['zhangjie_id' => $id]); //直接干掉, 不做软删除
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
