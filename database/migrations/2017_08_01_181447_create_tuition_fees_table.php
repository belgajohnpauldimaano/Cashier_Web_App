<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTuitionFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tuition_fees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('grade_id')->unsigned();
            $table->decimal('tuition_fee', 12, 2);
            $table->decimal('misc_fee', 12, 2);
            $table->string('school_year', 20);
            $table->tinyInteger('status')->default('1');
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
        Schema::dropIfExists('tuition_fees');
    }
}
