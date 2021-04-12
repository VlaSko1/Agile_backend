<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Str;
use Faker\Factory as Faker;

class BlogsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        foreach (range (1, 100) as $value) {
            $date = $faker->date($format = 'Y-m-d', $max = 'now');
            DB::table('blogs')->insert([
                'title' => $faker->sentence($faker->numberBetween(5, 9), true),
                'text' => $faker->realText($faker->numberBetween(500, 2000)),
                'blog_img' => null,
                'user_id' => $faker->numberBetween(1, 2),
                'category_id' => $faker->numberBetween(1, 5),
                'public' => '1',
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }   
}
