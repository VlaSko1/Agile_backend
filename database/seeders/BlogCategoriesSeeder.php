<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BlogCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('blog_categories')->insert([
            ['id' => 1, 'category_name' => 'животные'],
            ['id' => 2, 'category_name' => 'красота'],
            ['id' => 3, 'category_name' => 'политика'],
            ['id' => 4, 'category_name' => 'путешествия'],
            ['id' => 5, 'category_name' => 'семья'],
        ]);
    }
}
