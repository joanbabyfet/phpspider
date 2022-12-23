<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class seed_permissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $created_at = date('Y-m-d H:i:s');

        $fields = [
            'name',
            'guard_name',
            'display_name',
            'module_id',
            'created_at',
        ];

        $rows = [
            ['admin.user.index', 'admin', '用户列表', 1, $created_at],
            ['admin.user.login_log', 'admin', '登陆记录', 1, $created_at],
            ['admin.user.enable', 'admin', '解封用户', 1, $created_at],
            ['admin.user.disable', 'admin', '封禁用户', 1, $created_at],
            ['admin.user.black_list', 'admin', '黑名单', 1, $created_at],
            ['admin.user.update_amount', 'admin', '修改额度', 1, $created_at],
            ['admin.order_transfer.index', 'admin', '余额修改记录', 1, $created_at],
            ['admin.agent.index', 'admin', '渠道列表', 2, $created_at],
            ['admin.agent.add', 'admin', '新增渠道', 2, $created_at],
            ['admin.agent.edit', 'admin', '编辑渠道', 2, $created_at],
            ['admin.agent.enable', 'admin', '开启渠道', 2, $created_at],
            ['admin.agent.disable', 'admin', '关闭渠道', 2, $created_at],
            ['admin.report.member_online_list', 'admin', '用户在线列表', 3, $created_at],
            ['admin.report.export_member_online', 'admin', '用户在线导出', 3, $created_at],
            ['admin.report.member_active_list', 'admin', '用户活跃列表', 4, $created_at],
            ['admin.report.member_retention_list', 'admin', '用户留存列表', 5, $created_at],
            ['admin.report.member_increase_list', 'admin', '新增用户列表', 28, $created_at],
            ['admin.system.maintenance', 'admin', '获取维护通知', 12, $created_at],
            ['admin.system.edit_maintenance', 'admin', '维护通知设置', 12, $created_at],
            ['admin.role.index', 'admin', '角色列表', 17, $created_at],
            ['admin.role.detail', 'admin', '角色详情', 17, $created_at],
            ['admin.role.add', 'admin', '创建角色', 17, $created_at],
            ['admin.role.edit', 'admin', '编辑角色', 17, $created_at],
            ['admin.role.delete', 'admin', '删除角色', 17, $created_at],
            ['admin.admin_user.index', 'admin', '管理员列表', 18, $created_at],
            ['admin.admin_user.add', 'admin', '管理员创建', 18, $created_at],
            ['admin.admin_user.edit', 'admin', '管理员编辑', 18, $created_at],
            ['admin.admin_user.delete', 'admin', '管理员删除', 18, $created_at],
            ['admin.admin_user.enable', 'admin', '管理员启用', 18, $created_at],
            ['admin.admin_user.disable', 'admin', '管理员禁用', 18, $created_at],
            ['admin.admin_user_oplog.index', 'admin', '管理操作日志列表', 19, $created_at],
            ['admin.agent_oplog.index', 'admin', '代理操作日志列表', 20, $created_at],

            ['adminag.user.index', 'agent', '用户列表', 1, $created_at], //代理权限
            ['adminag.user.login_log', 'agent', '登陆记录', 1, $created_at],
            ['adminag.user.enable', 'agent', '解封用户', 1, $created_at],
            ['adminag.user.disable', 'agent', '封禁用户', 1, $created_at],
            ['adminag.user.black_list', 'agent', '黑名单', 1, $created_at],
            ['adminag.report.member_online_list', 'agent', '用户在线列表', 3, $created_at],
            ['adminag.report.export_member_online', 'agent', '用户在线导出', 3, $created_at],
            ['adminag.report.member_active_list', 'agent', '用户活跃列表', 4, $created_at],
            ['adminag.report.member_retention_list', 'agent', '用户留存列表', 5, $created_at],
            ['adminag.report.member_increase_list', 'agent', '新增用户列表', 28, $created_at],
            ['adminag.role.index', 'agent', '角色列表', 17, $created_at],
            ['adminag.role.add', 'agent', '创建角色', 17, $created_at],
            ['adminag.role.edit', 'agent', '编辑角色', 17, $created_at],
            ['adminag.role.delete', 'agent', '删除角色', 17, $created_at],
            ['adminag.agent.index', 'agent', '子帐号列表', 22, $created_at],
            ['adminag.agent.detail', 'agent', '子帐号详情', 22, $created_at],
            ['adminag.agent.add', 'agent', '新增子帐号', 22, $created_at],
            ['adminag.agent.edit', 'agent', '编辑子帐号', 22, $created_at],
            ['adminag.agent.delete', 'agent', '删除子帐号', 22, $created_at],
            ['adminag.agent.enable', 'agent', '开启子帐号', 22, $created_at],
            ['adminag.agent.disable', 'agent', '关闭子帐号', 22, $created_at],
        ];

        $insert_data = [];
        foreach ($rows as $row)
        {
            $item = [];
            foreach ($fields as $k => $field)
            {
                $item[$field] = $row[$k];
            }
            $insert_data[] = $item;
        }
        DB::table('permissions')->insert($insert_data);
    }
}
