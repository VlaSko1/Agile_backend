<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Str;
use Faker\Factory as Faker;

class CommentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        foreach (range (1, 250) as $value) {
            $date = $faker->dateTimeBetween($format = 'Y-m-d H_i_s', '-1 week', '+1 week');
            DB::table('comments')->insert([
                'comment' => $faker->realText($faker->numberBetween(10, 50), true),
                'user_id' => $faker->numberBetween(1, 2),
                'blog_id' => $faker->numberBetween(1, 100),
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}
