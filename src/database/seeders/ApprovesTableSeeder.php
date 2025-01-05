<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApprovesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'status' => '承認待ち'
        ];
        DB::table('approves')->insert($param);

        $param = [
            'status' => '承認済み'
        ];
        DB::table('approves')->insert($param);
    }
}
