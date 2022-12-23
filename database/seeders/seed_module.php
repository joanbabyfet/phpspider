<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class seed_module extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $created_at = time();

        $fields = [
            'name',
            'status',
            'create_time',
            'create_user',
        ];

        //模块id对应控制器里定义, 若增加新模块往下添加, 保持前面模块id不变
        $rows = [
            ['用户管理', 1, $created_at, '1'],
            ['渠道列表', 1, $created_at, '1'],
            ['用户在线', 1, $created_at, '1'],
            ['用户活跃', 1, $created_at, '1'],
            ['用户留存', 1, $created_at, '1'],
            ['新增用户', 1, $created_at, '1'],
            ['维护', 1, $created_at, '1'],
            ['角色列表', 1, $created_at, '1'],
            ['帐号列表', 1, $created_at, '1'],
            ['管理操作日志', 1, $created_at, '1'],
            ['代理操作日志', 1, $created_at, '1'],
            ['文章管理', 0, $created_at, '1'],
            ['子帐号管理', 1, $created_at, '1'],
            ['缓存管理', 0, $created_at, '1'],
            ['应用私匙', 0, $created_at, '1'],
            ['系统配置', 0, $created_at, '1'],
            ['菜单管理', 0, $created_at, '1'],
            ['个人中心', 1, $created_at, '1'],
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
        DB::table('module')->insert($insert_data);
    }
}
