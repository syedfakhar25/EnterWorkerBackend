<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('step_id');
            $table->bigInteger('project_id');
            $table->bigInteger('employee_id');
            $table->bigInteger('manager_id');
            $table->bigInteger('company_id');
            $table->string('title')->nullable();
           // $table->double('percentage');
            $table->date('deadline')->nullable();
            $table->bigInteger('task_status')->default(0);
            $table->bigInteger('company_worker')->nullable();
            $table->boolean('is_important');
            //$table->boolean('active');
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
        Schema::dropIfExists('tasks');
    }
}
