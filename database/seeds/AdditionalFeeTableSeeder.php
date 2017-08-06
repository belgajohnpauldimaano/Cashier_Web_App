<?php

use Illuminate\Database\Seeder;

class AdditionalFeeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        for ($i=1; $i<=23; $i++)
        {
            $TuitionFee = new App\AdditionalFee();
            $TuitionFee->grade_id               = $i;
            $TuitionFee->additional_title       = 'Books (Annually)';
            $TuitionFee->additional_amount      = 2000.00;
            $TuitionFee->school_year            = '2017-2018';
            $TuitionFee->save();

            $TuitionFee = new App\AdditionalFee();
            $TuitionFee->grade_id               = $i;
            $TuitionFee->additional_title       = 'Speech Lab (Annually)';
            $TuitionFee->additional_amount      = 500.00;
            $TuitionFee->school_year            = '2017-2018';
            $TuitionFee->save();

            $TuitionFee = new App\AdditionalFee();
            $TuitionFee->grade_id               = $i;
            $TuitionFee->additional_title       = 'P.E Uniform/Set';
            $TuitionFee->additional_amount      = 400.00;
            $TuitionFee->school_year            = '2017-2018';
            $TuitionFee->save();

            $TuitionFee = new App\AdditionalFee();
            $TuitionFee->grade_id               = $i;
            $TuitionFee->additional_title       = 'School Uniform/Set';
            $TuitionFee->additional_amount      = 400.00;
            $TuitionFee->school_year            = '2017-2018';
            $TuitionFee->save();
        }
    }
}
