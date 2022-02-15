<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id');
            $table->bigInteger('manager_id')->nullable();
            $table->bigInteger('company_worker_id')->nullable();
            $table->boolean('active')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('project_offer')->nullable();
            $table->string('project_drawing')->nullable();
            $table->text('offer_comment')->nullable();
            $table->text('drawing_comment')->nullable();
            $table->text('project_drawing')->nullable();
            $table->text('offer_with_price')->nullable();
            $table->text('contract')->nullable();

            //project actual address
            $table->string('street')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();

            //customer address fields in project
            $table->string('cus_address')->nullable();
            $table->string('cus_postal_code')->nullable();
            $table->string('cus_city')->nullable();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('status')->nullable();
            $table->bigInteger('percentage')->nullable();
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
        Schema::dropIfExists('projects');
    }
}
