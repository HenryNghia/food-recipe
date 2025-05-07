<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('accounts')->delete();

        // Reset id về lại 1
        DB::table('accounts')->truncate();

        // 2. Ta sẽ thêm mới danh mục bằng lệnh create
        DB::table('accounts')->insert([
            [
                'user_name' => 'admin',
                'password' => bcrypt(123456),
                'email' => 'admin@gmail.com',
                'id_roles' =>'1',
            ],

            [
                'user_name' => 'nghia',
                'password' => bcrypt(123456),
                'email' => 'nghia@gmail.com',
                'id_roles' =>'2',
            ],
        ]);
    }
}
