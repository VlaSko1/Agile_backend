<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Str;
use Faker\Factory as Faker;

class LikesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        foreach (range (1, 50) as $value) {
            $date = $faker->dateTimeBetween($format = 'Y-m-d H_i_s', '-1 week', '+1 week');
            DB::table('likes')->insert([
                'user_id' => $faker->numberBetween(1, 2),
                'blog_id' => $faker->unique()->numberBetween(1, 50),
                'isLike' => '1',
                'created_at' => $date,
                'updated_at' => $date,
            ]);
        }
    }
}
