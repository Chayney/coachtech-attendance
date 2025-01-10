<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'name' => '勤務外'
        ];
        DB::table('statuses')->insert($param);

        $param = [
            'name' => '出勤中'
        ];
        DB::table('statuses')->insert($param);

        $param = [
            'name' => '休憩中'
        ];
        DB::table('statuses')->insert($param);

        $param = [
            'name' => '退勤済'
        ];
        DB::table('statuses')->insert($param);
    }
}
