<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccountsPermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('accounts_permissions', function($table)
        {
            $table->integer('permission_id')->unsigned();
            $table->integer('account_id')->unsigned();
            $table->integer('value');

            $table->engine = 'InnoDB';
            $table->primary(['permission_id', 'account_id']);

            $table->foreign('permission_id')
            	->references('id')->on('permissions')
            	->onDelete('cascade')
            	->onUpdate('cascade');
            $table->foreign('account_id')
            	->references('id')->on('accounts')
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
		Schema::drop('accounts_permissions');
	}

}
