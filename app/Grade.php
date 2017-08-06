<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    public function tuition_fee ()
    {
        return $this->HasMany(TuitionFee::class, 'grade_id', 'id');
    }

    public function additional_fee ()
    {
        return $this->HasMany(AdditionalFee::class, 'grade_id', 'id');
    }
}
