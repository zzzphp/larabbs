<?php
/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Generator as Faker;

$factory->define(App\Models\UsersRelation::class, function (Faker $faker) {
    $date_time = $faker->date . ' ' . $faker->time;
    return [
        // 'name' => $faker->name,
        'relation_type' => 1,
        'created_at' => $date_time,
        'updated_at' => $date_time,
    ];
});
