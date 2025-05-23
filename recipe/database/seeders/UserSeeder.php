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

        DB::table('users')->delete();

        // Reset id về lại 1
        DB::table('users')->truncate();

        // 2. Ta sẽ thêm mới danh mục bằng lệnh create
        DB::table('users')->insert([
            [
                'name' => 'admin',
                'password' => bcrypt(123456),
                'email' => 'admin@gmail.com',
                'avatar' => 'https://i.pinimg.com/564x/24/21/85/242185eaef43192fc3f9646932fe3b46.jpg',
                'id_roles' =>'1',
            ],

            [
                'name' => 'nghia',
                'password' => bcrypt(123456),
                'email' => 'nghia@gmail.com',
                'avatar' => 'https://i.pinimg.com/564x/24/21/85/242185eaef43192fc3f9646932fe3b46.jpg',
                'id_roles' =>'2',
            ],
        ]);
    }
}
