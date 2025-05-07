<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('favorites')->delete();

        // Reset id về lại 1
        DB::table('favorites')->truncate();

        // 2. Ta sẽ thêm mới danh mục bằng lệnh create
        DB::table('favorites')->insert([
            [
                'user_id' => 2,
                'recipe_id' => 1,
                'saved_date' => now(),
            ],

            [
                'user_id' => 2,
                'recipe_id' => 2,
                'saved_date' => now(),
            ],

            [
                'user_id' => 2,
                'recipe_id' => 3,
                'saved_date' => now(),
            ],
        ]);
    }
}
