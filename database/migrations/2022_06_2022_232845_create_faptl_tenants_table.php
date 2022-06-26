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
        Schema::create('faptl_tenants', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->string('id', 36)->primary()->unique();
            $table->string('name', 50);
            $table->string('type', 36)->nullable();

            $table->string('date_of_birth',50)->nullable();
            $table->string('id_number')->nullable();
            $table->string('passport_number')->nullable();

            $table->string('gender')->nullable();
            $table->int('marit_status')->default(0)->nullable();
            $table->int( 'tenant_number')->nullable();

            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();

            $table->string('postal_code')->nullable();

            $table->string('business_name')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('business_industry')->nullable();
            $table->string('business_description')->nullable();
            $table->string('business_address')->nullable();

            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_email')->nullable();


            $table->string('employment_status')->nullable();
            $table->string('employment_position')->nullable();
         

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
        Schema::dropIfExists('faptl_tenants');
    }
}
