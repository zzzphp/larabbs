<?php

use Illuminate\Database\Seeder;
use App\Models\UsersRelation;

class UsersRelationsTableSeeder extends Seeder
{
    public function run()
    {
        $users_relations = factory(UsersRelation::class)->times(50)->make()->each(function ($users_relation, $index) {
            if ($index == 0) {
                // $users_relation->field = 'value';
            }
        });

        UsersRelation::insert($users_relations->toArray());
    }

}

