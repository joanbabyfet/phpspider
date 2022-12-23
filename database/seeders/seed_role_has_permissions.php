<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class seed_role_has_permissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fields = [
            'permission_id',
            'role_id',
        ];

        $rows = [
            [33, 3], //代理商
            [34, 3],
            [35, 3],
            [36, 3],
            [37, 3],
            [33, 4], //子帐号
            [34, 4],
            [35, 4],
            [36, 4],
            [37, 4],
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
        DB::table('role_has_permissions')->insert($insert_data);
    }
}
