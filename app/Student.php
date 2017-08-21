<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    public $timestamps = false;
    
    public function grade()
    {
        return $this->hasOne(Grade::class, 'id', 'grade_id');
    }

    public function section()
    {
        return $this->hasOne(Section::class, 'id', 'section_id');
    }

    public function tuition ()
    {
        return $this->hasMany(StudentTuitionFee::class, 'student_id', 'id');
    }

    public function discount ()
    {
        return $this->hasMany(StudentDiscount::class, 'student_id', 'id');
    }

    public function discount_list ()
    {
        return $this->hasOne(StudentDiscountList::class, 'student_id', 'id');
    }
    
    public function grade_tuition ()
    {
        return $this->hasMany(TuitionFee::class, 'grade_id', 'grade_id');
    }

    public function additional_fee ()
    {
        return $this->hasMany(AdditionalFee::class, 'grade_id', 'grade_id');
    }

    public function additional_fee_payment ()
    {
        return $this->hasOne(AdditionalFeePayment::class, 'student_id', 'id');
    }
}
