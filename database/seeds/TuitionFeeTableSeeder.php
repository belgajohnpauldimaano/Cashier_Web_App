<?php

use Illuminate\Database\Seeder;

class TuitionFeeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tuition_fees = [
            ['tuition_fee' => 20000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 20000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 20000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 20000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 20000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 20000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 20000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 20000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 20000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 20000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 20000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 20000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 20000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 20000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 20000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 20000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 20000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 18000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 19000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 20000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 21000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 22000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
            ['tuition_fee' => 23000.00, 'misc_fee' => 4000.00, 'upon_enrollment' => 4000.00],
        ];

        foreach ($tuition_fees as $key => $tuition)
        {
            $TuitionFee = new App\TuitionFee();
            $TuitionFee->grade_id       = $key + 1;
            $TuitionFee->tuition_fee    = $tuition['tuition_fee'];
            $TuitionFee->misc_fee       = $tuition['misc_fee'];
            $TuitionFee->upon_enrollment= $tuition['upon_enrollment'];
            $TuitionFee->school_year    = '2017-2018';
            $TuitionFee->save();
        }
    }
}
