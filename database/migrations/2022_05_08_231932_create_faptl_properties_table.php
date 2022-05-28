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
        Schema::create('faptl_properties', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->string('id', 36)->primary()->unique();

            $table->string('name')->nullable();

            $table->string('code')->nullable();

            $table->string('type')->nullable();

            $table->string('address')->nullable();

            $table->string('city')->nullable();

            $table->string('state')->nullable();

            $table->string('zip')->nullable();

            $table->string('note')->nullable();

            $table->float('rent_amount', 10, 2)->nullable()->default(0.00);

            $table->string('size')->nullable();

            $table->string('link')->nullable();

            $table->string('has_parking',5)->default(0);

            $table->string('has_security_gard',5)->default(0);

            $table->string('has_electricity',5)->default(0);

            $table->string('has_gas',5)->default(0);

            $table->string('has_swiming_pool',5)->default(0);

            $table->string('isFeatured')->default(0);

            $table->string('isActive')->default(0);

            $table->string('assign_user',36)->nullable();

            $table->text('short_description')->nullable();

            $table->text('long_description')->nullable();

            $table->string('number_units',10)->nullable();



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
        Schema::dropIfExists('faptl_properties');
    }
}
