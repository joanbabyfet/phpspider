<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class seed_menu extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fields = [
            'parent_id',
            'level',
            'name',
            'type',
            'guard_name',
            'url',
            'icon',
            'perms',
            'sort',
            'is_show',
            'status',
        ];

        $rows = [
            [0, 0, '用户数据查询', 1, 'admin', '/users/all_users', 'peoples', '', 0, 1, 1],
            [0, 0, '渠道', 1, 'admin', '/channel/channel_list', 'tab', '', 1, 1, 1],
            [0, 0, '数据统计', 1, 'admin', '/statistics/user_online', 'chart', '', 2, 1, 1],
            [0, 0, '后台管理', 1, 'admin', '/accountsetting/role', 'password', '', 6, 1, 1],
            [0, 0, '用户数据查询', 1, 'agent', '/users/all_users', 'peoples', '', 0, 1, 1], //代理一级菜单
            [0, 0, '数据统计', 1, 'agent', '/statistics/user_online', 'chart', '', 2, 1, 1],
            [0, 0, '后台管理', 1, 'agent', '/accountsetting/role', 'password', '', 6, 1, 1],
            [1, 1, '所有用户', 1, 'admin', '/users/all_users', '', 'admin.user.index', 0, 1, 1],
            [1, 1, '封禁用户', 1, 'admin', '', '', 'admin.user.disable', 0, 0, 1],
            [1, 1, '解封用户', 1, 'admin', '', '', 'admin.user.enable', 0, 0, 1],
            [1, 1, '登陆记录', 1, 'admin', '/users/login_record', '', 'admin.user.login_log', 0, 0, 1],
            [1, 1, '黑名单', 1, 'admin', '/users/blacklist', '', 'admin.user.black_list', 1, 1, 1],
            [1, 1, '修改额度', 1, 'admin', '', '', 'admin.user.update_amount', 0, 0, 1],
            [1, 1, '余额修改记录', 1, 'admin', '/users/balance_modification_record', '', 'admin.order_transfer.index', 2, 1, 1],
            [2, 1, '渠道列表', 1, 'admin', '/channel/channel_list', '', 'admin.agent.index', 0, 1, 1],
            [2, 1, '新增渠道', 1, 'admin', '', '', 'admin.agent.add', 0, 0, 1],
            [2, 1, '编辑渠道', 1, 'admin', '', '', 'admin.agent.edit', 0, 0, 1],
            [2, 1, '开启渠道', 1, 'admin', '', '', 'admin.agent.enable', 0, 0, 1],
            [2, 1, '关闭渠道', 1, 'admin', '', '', 'admin.agent.disable', 0, 0, 1],
            [3, 1, '用户在线', 1, 'admin', '/statistics/user_online', '', 'admin.report.member_online_list', 0, 1, 1],
            [3, 1, '用户在线导出', 1, 'admin', '', '', 'admin.report.export_member_online', 0, 0, 1],
            [3, 1, '用户活跃', 1, 'admin', '/statistics/user_active', '', 'admin.report.member_active_list', 1, 1, 1],
            [3, 1, '用户留存', 1, 'admin', '/statistics/user_retention', '', 'admin.report.member_retention_list', 2, 1, 1],
            [3, 1, '新增用户', 1, 'admin', '/statistics/user_growth', '', 'admin.report.member_increase_list', 3, 1, 1],
            [4, 1, '账号列表', 1, 'admin', '/accountsetting/role', '', 'admin.admin_user.index', 0, 1, 1],
            [4, 1, '账号创建', 1, 'admin', '', '', 'admin.admin_user.add', 0, 0, 1],
            [4, 1, '账号编辑', 1, 'admin', '', '', 'admin.admin_user.edit', 0, 0, 1],
            [4, 1, '账号删除', 1, 'admin', '', '', 'admin.admin_user.delete', 0, 0, 1],
            [4, 1, '账号启用', 1, 'admin', '', '', 'admin.admin_user.enable', 0, 0, 1],
            [4, 1, '账号禁用', 1, 'admin', '', '', 'admin.admin_user.disable', 0, 0, 1],
            [4, 1, '角色列表', 1, 'admin', '/accountsetting/permission', '', 'admin.role.index', 1, 1, 1],
            [4, 1, '创建角色', 1, 'admin', '', '', 'admin.role.add', 0, 0, 1],
            [4, 1, '编辑角色', 1, 'admin', '', '', 'admin.role.edit', 0, 0, 1],
            [4, 1, '删除角色', 1, 'admin', '', '', 'admin.role.delete', 0, 0, 1],
            [4, 1, '角色详情', 1, 'admin', '', '', 'admin.role.detail', 0, 0, 1],
            [4, 1, '管理操作日志', 1, 'admin', '/accountsetting/operation_log', '', 'admin.admin_user_oplog.index', 2, 1, 1],
            [4, 1, '代理操作日志', 1, 'admin', '/accountsetting/agent_log', '', 'admin.agent_oplog.index', 3, 1, 1],

            [5, 1, '所有用户', 1, 'agent', '/users/all_users', '', 'adminag.user.index', 0, 1, 1], //代理二级菜单
            [5, 1, '封禁用户', 1, 'agent', '', '', 'adminag.user.disable', 0, 0, 1],
            [5, 1, '解封用户', 1, 'agent', '', '', 'adminag.user.enable', 0, 0, 1],
            [5, 1, '登陆记录', 1, 'agent', '/users/login_record', '', 'adminag.user.login_log', 0, 0, 1],
            [5, 1, '黑名单', 1, 'agent', '/users/blacklist', '', 'adminag.user.black_list', 1, 1, 1],
            [6, 1, '用户在线', 1, 'agent', '/statistics/user_online', '', 'adminag.report.member_online_list', 0, 1, 1],
            [6, 1, '用户在线导出', 1, 'agent', '', '', 'adminag.report.export_member_online', 0, 0, 1],
            [6, 1, '用户活跃', 1, 'agent', '/statistics/user_active', '', 'adminag.report.member_active_list', 1, 1, 1],
            [6, 1, '用户留存', 1, 'agent', '/statistics/user_retention', '', 'adminag.report.member_retention_list', 2, 1, 1],
            [6, 1, '新增用户', 1, 'agent', '/statistics/user_growth', '', 'adminag.report.member_increase_list', 3, 1, 1],
            [7, 1, '子账号列表', 1, 'agent', '/accountsetting/permission', '', 'adminag.agent.index', 0, 1, 1],
            [7, 1, '子账号详情', 1, 'agent', '', '', 'adminag.agent.detail', 0, 0, 1],
            [7, 1, '子账号创建', 1, 'agent', '', '', 'adminag.agent.add', 0, 0, 1],
            [7, 1, '子账号编辑', 1, 'agent', '', '', 'adminag.agent.edit', 0, 0, 1],
            [7, 1, '子账号删除', 1, 'agent', '', '', 'adminag.agent.delete', 0, 0, 1],
            [7, 1, '子账号启用', 1, 'agent', '', '', 'adminag.agent.enable', 0, 0, 1],
            [7, 1, '子账号禁用', 1, 'agent', '', '', 'adminag.agent.disable', 0, 0, 1],
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
        DB::table('menu')->insert($insert_data);
    }
}
