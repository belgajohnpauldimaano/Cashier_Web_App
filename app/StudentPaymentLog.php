<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentPaymentLog extends Model
{
    public function student ()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function user ()
    {
        return $this->belongsTo(User::class, 'received_by', 'id');
    }
    
}
