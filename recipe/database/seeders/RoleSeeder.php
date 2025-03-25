<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
   /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->delete();

        // Reset id về lại 1
        DB::table('roles')->truncate();

        // 2. Ta sẽ thêm mới danh mục bằng lệnh create
        DB::table('roles')->insert([
            [
                'name_role'  => 'admin',
            ],
            [
                'name_role'  => 'user',
            ],
        ]);
    }
}
