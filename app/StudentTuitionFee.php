<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentTuitionFee extends Model
{
    public $timestamps = false;

    const PAYMENT_STATUS = [
        ['CLASS_STYLE' => 'label bg-red' , 'TEXT_DISPLAY' => 'Not yet paid'], 
        ['CLASS_STYLE' => 'label bg-green' , 'TEXT_DISPLAY' => 'Fully paid'], 
    ];

    public function student ()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
}
