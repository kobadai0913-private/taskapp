<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeopleTableSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        // $param = [
        //     'id' => 1,
        //     'name' => 'taro',
        //     'mail' => 'taro@yamada.jp',
        //     'age' => 12,
        // ];
        // DB::table('people')->insert($param);

        // $param = [
        //     'id' => 2,
        //     'name' => 'hanako',
        //     'mail' => 'hanako@flower.jp',
        //     'age' => 34,
        // ];
        // DB::table('people')->insert($param);

        // $param = [
        //     'id' => 3,
        //     'name' => 'sachiko',
        //     'mail' => 'sachiko@happy.jp',
        //     'age' => 56,
        // ];
        // DB::table('people')->insert($param);

        $param = [
            'user_id' => 1,
            'user_pass' => 'kobayashi',
            'user_name' => 'kobayashidaisuke',
            'user_mail' => 'kobayashi@suncreer.co.jp',
        ];
        DB::table('user')->insert($param);

    }
}
