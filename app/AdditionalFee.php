<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdditionalFee extends Model
{
    const ADDITIONAL_FEES = [
        2 => ['fee_name' => 'Books (Annually)',      'css_style' => 'bg-orange'],
        3 => ['fee_name' => 'Speech Lab (Annually)', 'css_style' => 'bg-purple'],
        4 => ['fee_name' => 'P.E Uniform/Set',       'css_style' => 'bg-aqua'],
        5 => ['fee_name' => 'School Uniform/Set',    'css_style' => 'bg-red'],
        6 => ['fee_name' => 'Government Subsidy',    'css_style' => 'bg-blue']
    ];
}
