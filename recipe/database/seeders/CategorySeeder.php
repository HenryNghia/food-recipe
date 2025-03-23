<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->delete();

        // Reset id về lại 1
        DB::table('categories')->truncate();

        // 2. Ta sẽ thêm mới danh mục bằng lệnh create
        DB::table('categories')->insert([
            [
                'name_category'  => 'beef',
                'image' => 'https://www.shutterstock.com/image-photo/raw-beef-steak-rosemary-peppercorns-600nw-2292399653.jpg'
            ],
            [
                'name_category' => 'pork',
                'image' => 'https://pinchandswirl.com/wp-content/uploads/2024/07/Pork-Belly-Recipe-sq.jpg'
            ],
            [
                'name_category' => 'lamp',
                'image' => 'https://www.allrecipes.com/thmb/7HxjNoBySy-r-qG6r7MFOAuQCr4=/1500x0/filters:no_upscale():max_bytes(150000):strip_icc()/74037-lamb-chops-with-balsamic-reduction-DDMFS-step-4x3-158-cc81d0fe528c4b07be2d7031e152f70b.jpg'
            ],
            [
                'name_category' => 'chicken',
                'image' => 'https://www.allrecipes.com/thmb/GQhn2Qica6M6tH2N9ggFXwYQjUI=/0x512/filters:no_upscale():max_bytes(150000):strip_icc():format(webp)/AR-93168-rotisserie-chicken-hero-4x3-ee1ae8718d494a21bba95c996604a077.jpg'
            ],
            [
                'name_category' => 'duck',
                'image' => 'https://assets.epicurious.com/photos/5c93f15d7903444d883ded50/1:1/w_2560%2Cc_limit/Crisp-Roast-Duck-19032019.jpg'
            ],
            [
                'name_category' => 'egg',
                'image' => 'https://www.thisfarmgirlcooks.com/wp-content/uploads/2019/02/overhead-oven-baked-eggs-scaled.jpg'
            ],
            [
                'name_category' => 'fish',
                'image' => 'https://static01.nyt.com/images/2019/01/23/dining/23Romanrex1/merlin_148945323_3aa45d42-3e15-4efa-8700-2a4f22910719-videoSixteenByNineJumbo1600.jpg'
            ],
            [
                'name_category' => 'vegetable',
                'image' => 'http://assistinghands.com/6/wp-content/uploads/sites/29/2019/01/Eat-Vegetables.jpg'
            ],
        ]);

    }
}
