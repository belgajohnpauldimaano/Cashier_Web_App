<?php

use Illuminate\Database\Seeder;

class GradeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $grades = [
            'Toddler',
            'Nursery',
            'Kinder',
            'Grade 1',
            'Grade 2',
            'Grade 3',
            'Grade 4',
            'Grade 5',
            'Grade 6',
            'Grade 7',
            'Grade 8',
            'Grade 9',
            'Grade 10',
            'Grade 11 - STEM',
            'Grade 11 - ABM',
            'Grade 11 - HUMSS',
            'Grade 11 - GAS',
            'Grade 11 - TVL',
            'Grade 12 - STEM',
            'Grade 12 - ABM',
            'Grade 12 - HUMSS',
            'Grade 12 - GAS',
            'Grade 12 - TVL',
        ];

        foreach ($grades as $grade)
        {
            $Section = new App\Grade();
            $Section->grade  = $grade;
            $Section->save();
        }
    }
}
