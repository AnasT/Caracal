<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssignedRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('assigned_roles', function($table)
        {
            $table->integer('account_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->engine = 'InnoDB';
            $table->primary(['account_id', 'role_id']);

            $table->foreign('account_id')
            	->references('id')->on('accounts')
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
		Schema::drop('assigned_roles');
	}

}
