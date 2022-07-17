<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenantsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('faptl_fleases', function (Blueprint $table) {
			$table->engine = 'InnoDB';

			$table->increments('id');
			$table->int('property_id');
			$table->int('unit_id');

			$table->string('lease_type');
			$table->int('rent_amount');
			$table->string('lease_start');
			$table->string('lease_end');
			$table->int('deposit_amount');
			$table->int('late_fee_amount');
			$table->string('isActive');
		
			$table->string('created_by', 36)->nullable();
			$table->string('updated_by', 36)->nullable();
			$table->string('deleted_by', 36)->nullable();

			$table->softDeletes();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('faptl_fleases');
	}
}
