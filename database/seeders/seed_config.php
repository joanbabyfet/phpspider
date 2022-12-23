<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class seed_config extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fields = [
            'type',
            'name',
            'value',
            'title',
            'group',
            'sort',
        ];

        $rows = [
            ['string', 'site_name', 'Laravel开发框架', '主站名称', 'config', 0],
            ['string', 'site_description', 'Laravel开发框架', '主站摘要信息', 'config', 0],
            ['string', 'site_keyword', 'Laravel开发框架', '主站关键字', 'config', 0],
            ['string', 'maintenance_title', '', '维护通知标题', 'config', 0],
            ['string', 'maintenance_content', '', '维护通知内容', 'config', 0],
            ['int', 'sys_in_maintenance', 0, '系统是否维护中', 'config', 0],
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
        DB::table('config')->insert($insert_data); //走批量插入
    }
}
