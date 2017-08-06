<?php

use Illuminate\Database\Seeder;

class DiscountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $Discount = new App\Discount();
        $Discount->discount_title = 'Government Subsidy-SV & PEAC-FAPE';
        $Discount->discount_amount = '1000';
        $Discount->save();

        $Discount = new App\Discount();
        $Discount->discount_title = 'Family Membership';
        $Discount->discount_amount = '500';
        $Discount->save();

        $Discount = new App\Discount();
        $Discount->discount_title = 'NBI Alumni';
        $Discount->discount_amount = '2000';
        $Discount->save();
    }
}
