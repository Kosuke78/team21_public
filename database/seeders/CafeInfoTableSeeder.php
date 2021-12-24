<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CafeInfoTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cafe_infos')->insert([
            ['id' => '1',
            'place_id' => 'a',
            'user_id' => '1',
            ],
            [
            'id' => '2',
            'place_id' => 'b',
            'user_id' => '2',
            ],
        ]);
    }
}
