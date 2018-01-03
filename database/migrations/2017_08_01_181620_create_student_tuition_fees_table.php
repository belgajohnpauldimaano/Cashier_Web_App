<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentTuitionFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_tuition_fees', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id')->unsigned();
            $table->decimal('down_payment', 12, 2)->default('0.00');
            $table->decimal('monthly_payment', 12, 2)->default('0.00');
            $table->decimal('month_1_payment', 12, 2)->default('0.00');
            $table->decimal('month_2_payment', 12, 2)->default('0.00');
            $table->decimal('month_3_payment', 12, 2)->default('0.00');
            $table->decimal('month_4_payment', 12, 2)->default('0.00');
            $table->decimal('month_5_payment', 12, 2)->default('0.00');
            $table->decimal('month_6_payment', 12, 2)->default('0.00');
            $table->decimal('month_7_payment', 12, 2)->default('0.00');
            $table->decimal('month_8_payment', 12, 2)->default('0.00');
            $table->decimal('month_9_payment', 12, 2)->default('0.00');
            $table->decimal('month_10_payment', 12, 2)->default('0.00');
            $table->decimal('total_payment', 12, 2)->default('0.00');
            $table->decimal('total_remaining', 12, 2)->default('0.00');
            $table->decimal('total_tuition', 12, 2)->default('0.00');
            $table->decimal('gov_subsidy', 12, 2)->default('0.00');
            $table->decimal('additional_fee_remaining', 12, 2)->default('0.00');
            $table->decimal('additional_fee_total', 12, 2)->default('0.00');
            $table->decimal('total_discount', 12, 2)->default('0.00');
            $table->tinyInteger('fully_paid')->default('0');
            $table->string('school_year', 20);
            $table->integer('school_year_id')->unsigned();
            $table->tinyInteger('status')->default('1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_tuition_fees');
    }
}
