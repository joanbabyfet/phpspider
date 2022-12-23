<?php

namespace App\Http\Controllers\admin;

use App\repositories\repo_admin_user_oplog;
use App\repositories\repo_config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

/**
 * 系统配置
 * Class ctl_system
 * @package App\Http\Controllers\admin
 */
class ctl_system extends Controller
{
    private $repo_config;
    private $repo_admin_user_oplog;
    private $module_maintenance_id;
    private $module_table_id;
    private $module_game_id;

    public function __construct(
        repo_config $repo_config,
        repo_admin_user_oplog $repo_admin_user_oplog
    )
    {
        parent::__construct();
        $this->repo_config          = $repo_config;
        $this->repo_admin_user_oplog = $repo_admin_user_oplog;
        $this->module_maintenance_id = 12;
        $this->module_table_id = 10;
        $this->module_game_id = 11;
    }

    /**
     * 系统维护配置
     * @param Request $request
     * @return mixed
     */
    public function edit_maintenance(Request $request)
    {
        //系统是否维护中
        $sys_in_maintenance = $request->input('sys_in_maintenance', 0);
        $config_fields = [
            'maintenance_title'     => 'config', //config为分组
            'maintenance_content'   => 'config',
            'sys_in_maintenance'    => 'config',
        ];

        $data = [];
        foreach ($config_fields as $k => $v)
        {
            $data[] = [
                'name'          => $k,
                'value'         => $request->input($k, ''),
                'group'         => $v,
                'update_time'   => time(),
                'update_user'   => defined('AUTH_UID') ? AUTH_UID : '',
            ];
        }
        //批量更新
        $status = $this->repo_config->insertOrUpdate($data,
            ['name'],
            ['value', 'group', 'update_time', 'update_user']
        );
        if($status < 0)
        {
            return res_error($this->repo_config->get_err_msg($status), $status);
        }
        //更新缓存
        $this->repo_config->cache(true);
        //寫入日志
        $this->repo_admin_user_oplog->add_log("维护配置 ", $this->module_maintenance_id);
        //调用维护中命令
        $sys_in_maintenance == 1 ? Artisan::call('down') : Artisan::call('up');

        return res_success([], trans('api.api_update_success'));
    }

    /**
     * 获取系统维护配置
     * @param Request $request
     * @return mixed
     */
    public function maintenance(Request $request)
    {
        //系统是否维护中
        $sys_in_maintenance = $this->repo_config->get('sys_in_maintenance', [
            'type' => 'int', 'default' => 0, 'group' => 'config'
        ]);
        //维护通知标题
        $maintenance_title = $this->repo_config->get('maintenance_title', [
            'type' => 'string', 'default' => '', 'group' => 'config'
        ]);
        //维护通知内容
        $maintenance_content = $this->repo_config->get('maintenance_content', [
            'type' => 'text', 'default' => '', 'group' => 'config'
        ]);
        $maintenance_content = htmlspecialchars_decode($maintenance_content);

        return res_success([
            'sys_in_maintenance'    => $sys_in_maintenance,
            'maintenance_title'     => $maintenance_title,
            'maintenance_content'    => $maintenance_content,
        ]);
    }
}
