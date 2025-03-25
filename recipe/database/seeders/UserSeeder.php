<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
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
                'avatar' => 'https://i.pinimg.com/564x/24/21/85/242185eaef43192fc3f9646932fe3b46.jpg',
                'id_roles' =>'1',
            ],

            [
                'user_name' => 'nghia',
                'password' => bcrypt(123456),
                'email' => 'nghia@gmail.com',
                'avatar' => 'https://i.pinimg.com/564x/24/21/85/242185eaef43192fc3f9646932fe3b46.jpg',
                'id_roles' =>'2',
            ],


        ]);
    }
}
