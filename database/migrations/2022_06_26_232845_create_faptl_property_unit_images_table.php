<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaptlPropertyImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faptl_property_unit_images', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->string('id', 36)->primary()->unique();
            
            $table->string('unit_id');
            $table->string('file_name');
            $table->string('alt_text')->nullable();
            $table->integer('isActive',5)->default(0);
            $table->integer('isFeatured',5)->default(0);


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
        Schema::dropIfExists('faptl_property_unit_images');
    }
}
