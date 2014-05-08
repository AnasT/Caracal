<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionsRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('permissions_roles', function($table)
        {
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned();
            $table->integer('value');

            $table->engine = 'InnoDB';
            $table->primary(['permission_id', 'role_id']);

            $table->foreign('permission_id')
            	->references('id')->on('permissions')
            	->onDelete('cascade')
            	->onUpdate('cascade');
            $table->foreign('role_id')
            	->references('id')->on('roles')
            	->onDelete('cascade')
            	->onUpdate('cascade');
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('permissions_roles');
	}

}
