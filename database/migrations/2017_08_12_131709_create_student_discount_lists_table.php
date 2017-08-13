<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentDiscountListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_discount_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('scholar', 12, 2)->default('0.00');
            $table->decimal('school_subsidy', 12, 2)->default('0.00');
            $table->decimal('employee_scholar', 12, 2)->default('0.00');
            $table->decimal('gov_subsidy', 12, 2)->default('0.00');
            $table->decimal('acad_scholar', 12, 2)->default('0.00');
            $table->decimal('family_member', 12, 2)->default('0.00');
            $table->decimal('nbi_alumni', 12, 2)->default('0.00');
            $table->decimal('cash_discount', 12, 2)->default('0.00');
            $table->decimal('cwoir_discount', 12, 2)->default('0.00');
            $table->decimal('st_joseph_discount', 12, 2)->default('0.00');
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
        Schema::dropIfExists('student_discount_lists');
    }
}
