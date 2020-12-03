<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersRelationsTable extends Migration
{
	public function up()
	{
		Schema::create('users_relations', function(Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('user_id')->unsigned()->index();
            $table->bigInteger('follower_id')->unsigned();
            $table->tinyInteger('relation_type')->unsigned();
            $table->timestamps();
        });
	}

	public function down()
	{
		Schema::drop('users_relations');
	}
}
