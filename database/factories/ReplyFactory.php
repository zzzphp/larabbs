<?php
/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(App\Models\Reply::class, function (Faker $faker) {
    $time = $faker->dateTimeThisMonth;
    return [
         'content' => $faker->sentence(),
         'created_at' => $time,
         'updated_at' => $time,
         'topic_id' => mt_rand(1,50),
         'user_id' => mt_rand(1,10)
    ];
});
