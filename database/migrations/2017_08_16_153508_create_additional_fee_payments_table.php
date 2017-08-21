<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdditionalFeePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_fee_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('books', 12, 2)->default('0.00');
            $table->decimal('speech_lab', 12, 2)->default('0.00');
            $table->decimal('pe_uniform', 12, 2)->default('0.00');
            $table->decimal('school_uniform', 12, 2)->default('0.00');
            $table->integer('student_id')->unsigned();
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
        Schema::dropIfExists('additional_fee_payments');
    }
}
