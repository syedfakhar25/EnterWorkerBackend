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
            $table->string('manager_type')->nullable();
            $table->bigInteger('company_worker_id')->nullable();
            $table->boolean('active')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('project_offer')->nullable();
            $table->string('project_drawing')->nullable();
            $table->string('street')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('status')->nullable();
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
