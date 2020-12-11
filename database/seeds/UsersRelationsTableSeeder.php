<?php

use Illuminate\Database\Seeder;
use App\Models\UsersRelation;

class UsersRelationsTableSeeder extends Seeder
{
    public function run()
    {
        // 所有用户 ID 数组
        $user_ids = \App\Models\User::all()->pluck('id')->toArray();
        $faker = app(\Faker\Generator::class);
        $users_relations = factory(UsersRelation::class)->times(50)->make()->each(function ($users_relation, $index) use ($user_ids, $faker) {
            $user_id = $faker->randomElement($user_ids);
            $users_relation->user_id = $user_id;
            $follower_id = $faker->randomElement($user_ids);
            if ($follower_id == $user_id) {
                $follower_id = $faker->randomElement($user_ids);
            }
            $users_relation->follower_id = $follower_id;
            // 关注用户不能相同
//            while ($follower_id == $user_id) {
//                // 重新选取被关注用户
//                $follower_id = $faker->randomElement($user_ids);
//                if ($follower_id != $user_id) {
//                    $users_relation->follower_id = $follower_id;
//                    break;
//                }
//            }

        });

        UsersRelation::insert($users_relations->toArray());
    }

}

