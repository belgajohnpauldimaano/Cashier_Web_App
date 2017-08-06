<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SectionTableSeeder::class);
        $this->call(GradeTableSeeder::class);
        $this->call(TuitionFeeTableSeeder::class);
        $this->call(AdditionalFeeTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(DiscountsTableSeeder::class);
    }
}
