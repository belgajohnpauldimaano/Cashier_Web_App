<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    const DISCOUNT_TYPES = [
        // 'All',
        1 => ['type' => 'Full Scholar', 'css_style' => 'bg-orange'],
        2 => ['type' => 'School Subsidy', 'css_style' => 'bg-red'],
        3 => ['type' => 'Employee Scholar','css_style' => 'bg-primary'],
        4 => ['type' => 'Acad Scholar','css_style' => 'bg-purple'],
        5 => ['type' => 'Family Member','css_style' => 'bg-yellow'],
        6 => ['type' => 'NBI Alumni','css_style' => 'bg-gray'],
        7 => ['type' => 'Cash Discount','css_style' => 'bg-teal'],
        8 => ['type' => 'Choir Discount','css_style' => 'bg-maroon'],
        9 => ['type' => 'St. Joseph Discount','css_style' => 'bg-navy'],
    ];
}
