<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('levels')->delete();

        // Reset id về lại 1
        DB::table('levels')->truncate();

        // 2. Ta sẽ thêm mới danh mục bằng lệnh create
        DB::table('levels')->insert([
            [
                'name_level'  => 'dễ',
            ],
            [
                'name_level'  => 'trung bình',
            ],
            [
                'name_level'  => 'khó',
            ],
        ]);
    }
}
