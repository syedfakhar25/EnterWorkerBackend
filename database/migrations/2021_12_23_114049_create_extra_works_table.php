<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtraWorksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extra_works', function (Blueprint $table) {
            $table->id();
            $table->text('task_details')->nullable();
            $table->date('date')->nullable();
            $table->bigInteger('hours')->nullable();
            $table->string('created_by')->nullable();
            $table->bigInteger('project_id')->nullable();
            $table->bigInteger('employee_id')->nullable();
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
        Schema::dropIfExists('extra_works');
    }
}
