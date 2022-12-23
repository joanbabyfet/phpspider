<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class seed_category extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fields = [
            'pid',
            'name',
            'items',
            'sort',
        ];

        $rows = [
            [0, '玄幻魔法', 0, 0],
            [0, '武侠修真', 0, 0],
            [0, '都市言情', 0, 0],
            [0, '历史穿越', 0, 0],
            [0, '恐怖悬疑', 0, 0],
            [0, '游戏竞技', 0, 0],
            [0, '军事科幻', 0, 0],
            [0, '综合类型', 0, 0],
            [0, '女生频道', 0, 0],
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
        DB::table('category')->insert($insert_data);
    }
}
