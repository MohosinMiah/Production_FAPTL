<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaptlPropertiesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('faptl_property_units', function (Blueprint $table) {

			$table->engine = 'InnoDB';

			$table->string('id', 36)->primary()->unique();

			$table->string('property_id');

			$table->string('type');

			$table->string('name')->nullable();

			$table->integer('floor');

			$table->decimal( 'rent', 10, 2 );

			$table->decimal( 'garadge_fee', 10, 2 )->nullable();

			$table->decimal( 'electricity_fee', 10, 2 )->nullable();

			$table->decimal( 'gas_fee', 10, 2 )->nullable();

			$table->decimal( 'water_fee', 10, 2 )->nullable();

			$table->decimal( 'service_fee' , 10, 2 )->nullable();

			$table->integer('size');

			$table->integer('total_room');

			$table->integer('bed_room');

			$table->integer('bath_room');

			$table->integer('balcony');

			$table->text('note')->nullable();



			$table->string('isAvailable')->default(1);

			$table->string('isActive')->default(1);

			$table->string('isFeatured')->default(0);

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
		Schema::dropIfExists('faptl_property_units');
	}
}
