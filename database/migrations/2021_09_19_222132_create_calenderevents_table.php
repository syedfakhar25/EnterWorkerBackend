<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalendereventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calenderevents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('task_id')->nullable();
            $table->string('title')->nullable();
            $table->string('color')->nullable();
            $table->string('allDay')->nullable();
            $table->string('draggable')->nullable();
            $table->string('resizable')->nullable();
            $table->timestamp('start')->useCurrent();
            $table->timestamp('end')->useCurrent();
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
        Schema::dropIfExists('calenderevents');
    }
}
