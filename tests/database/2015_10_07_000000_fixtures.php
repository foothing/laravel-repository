<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Fixtures extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		// Drop laravel default users table.
		Schema::dropIfExists('entity');

		if ( ! Schema::hasTable('person') ) {
			Schema::create('person', function(Blueprint $table) {
				$table->integer('id')->unsigned()->index();
				$table->string('name');
			});
		}

		//
		//
		//	one-to-many
		//
		//

		if ( ! Schema::hasTable('son') ) {
			Schema::create('son', function(Blueprint $table) {
				$table->increments('id')->unsigned();
				$table->integer('pid')->unsigned();
				$table->string('name');
				$table->foreign('pid')->references('id')->on('person')->onDelete('cascade');
			});
		}

		//
		//
		//	many-to-many
		//
		//

		if ( ! Schema::hasTable('roles') ) {
			Schema::create('roles', function(Blueprint $table) {
				$table->integer('id')->unsigned()->index();
				$table->string('name');
			});
		}

		if ( ! Schema::hasTable('person_roles') ) {
			Schema::create('person_roles', function(Blueprint $table) {
				$table->integer('pid')->unsigned();
				$table->integer('rid')->unsigned();
				$table->foreign('pid')->references('id')->on('person')->onDelete('cascade');
				$table->foreign('rid')->references('id')->on('roles');
				$table->primary(array('pid', 'rid'));
			});
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()	{
		Schema::dropIfExists('person_roles');
		Schema::dropIfExists('son');
		Schema::dropIfExists('person');
		Schema::dropIfExists('roles');
	}

}
