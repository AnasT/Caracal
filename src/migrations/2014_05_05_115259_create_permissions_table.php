<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('permissions', function($table)
        {
            $table->increments('id');
            $table->string('name');
            $table->string('display_name');
            $table->text('description');
            $table->timestamps();

            $table->engine = 'InnoDB';
            $table->unique('name');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('permissions');
	}

}
