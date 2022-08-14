<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskManageseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'task_id' => 1,
            'task_name' => '研修',
            'task_detail' => 'Laravel研修',
        ];
        DB::table('user_taskmanage')->insert($param);
    }
}
