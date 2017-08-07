<?php

namespace App\Http\Controllers\Cashier;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use Carbon;
use DB;

use PDF;

use App\Student;
use App\Grade;
use App\Section;
use App\StudentTuitionFee;
use App\StudentPaymentLog;

class StudentPaymentController extends Controller
{
    public function index ()
    {

        $Students = Student::with(['grade', 
                                    'section', 
                                    'tuition' => function ($query) {
                                        $query->where('status', 1);
                                    }
                                ])
                                ->where('status', 1)
                                ->paginate(10);
        $Grade = Grade::all();
        $Section = Section::where('grade_id', 1)->get();
        
        // return json_encode($Students);
        return view('cashier.student_payment.index', ['Students' => $Students, 'Grade' => $Grade, 'Section' => $Section]);
    }

    public function fetch_data (Request $request)
    {
        $pages = 10;
        if ($request->show_count == '')
        {
            $pages = 0;
        }
        else
        {
            $pages = $request->show_count;
        }
        $Students = Student::with(['grade', 
                                    'section', 
                                    'tuition' => function ($query) {
                                        $query->where('status', 1);
                                    }
                                ])
                                ->where(function ($query) use ($request){
                                    $query->whereRaw("concat(first_name, ' ', middle_name , ' ', last_name) like '%". $request->search_filter ."%' ");

                                    if ($request->filter_grade)
                                    {
                                        $query->where('grade_id', $request->filter_grade);
                                    }

                                    if ($request->filter_section)
                                    {
                                        $query->where('section_id', $request->filter_section);
                                    }
                                })
                                ->where('status', 1)
                                ->paginate($pages);
                                
        return view('cashier.student_payment.partials.data_list', ['Students' => $Students, 'request' => $request->all()])->render();
    }

    public function show_form_modal_additional_payment (Request $request)
    {
        if (!$request->id)
        {

        }

        $Student = Student::with([
                                    'grade', 
                                    'section', 
                                    'tuition' => function ($query) {
                                        $query->where('status', 1);
                                    },
                                    'grade.tuition_fee' => function ($query) {
                                        $query->where('status', 1);
                                    },
                                ])
                                ->where('status', 1)
                                ->where('id', $request->id)
                                ->first();
        
        
        $StudentTuitionFee = StudentTuitionFee::where('student_id', $request->id)->first();

        $additional_amount = $StudentTuitionFee->additional_fee;
        
        
        return view('cashier.student_payment.partials.form_modal_additional_payment', ['additional_amount' => $additional_amount, 'student_id' => $StudentTuitionFee->student_id, 'Student' => $Student])->render();
    }

    public function additional_fee_payment_process (Request $request)
    {
        if ($request->payment < 1)
        {
            return json_encode(['code' => 2, 'general_message' => 'Invalid payment.']);
        }



        $StudentTuitionFee = StudentTuitionFee::where('student_id', $request->id)->first();
        $StudentTuitionFee->additional_fee -= $request->payment;
        $StudentTuitionFee->save();

        $StudentPaymentLog = new StudentPaymentLog();
        $StudentPaymentLog->student_id  = $request->id;
        $StudentPaymentLog->payment     = $request->payment;
        $StudentPaymentLog->payment_type= 2;
        $StudentPaymentLog->received_by = 1;
        $StudentPaymentLog->save();
        return json_encode(['code' => 0 ,'general_message' => 'Payment success', 'StudentTuitionFee' => $StudentTuitionFee]);

    }

    public function show_form_modal_pay_tuition (Request $request) 
    {
        if (!$request->id)
        {

        }

        $Student = Student::with([
                                    'grade', 
                                    'section', 
                                    'tuition' => function ($query) {
                                        $query->where('status', 1);
                                    },
                                    'grade.tuition_fee' => function ($query) {
                                        $query->where('status', 1);
                                    },
                                ])
                                ->where('status', 1)
                                ->where('id', $request->id)
                                ->first();
        
        
        $StudentTuitionFee = StudentTuitionFee::where('student_id', $request->id)->first();

        $monthly_amount = 0;
        if ($StudentTuitionFee->monthly_payment == 0)
        {
            $monthly_amount = $StudentTuitionFee->total_remaining / 10;
            $StudentTuitionFee->monthly_payment = $monthly_amount;
        }
        else
        {
            $monthly_amount = $StudentTuitionFee->monthly_payment;
        }

        $tuition_amount = $monthly_amount ;
        if ($StudentTuitionFee->down_payment == 0)
        {
            $tuition_amount = $Student->grade->tuition_fee[0]->misc_fee + 2000;
        }
        else
        {
            if ($StudentTuitionFee->total_remaining < $monthly_amount)
            {
                $tuition_amount = $StudentTuitionFee->total_remaining ;
            }
        }

        // return json_encode(['Student' => $Student, 'tuition_amount' => $tuition_amount]);

        $remaining_tuition = $Student->tuition[0]->total_remaining;
        
        return view('cashier.student_payment.partials.form_modal_tuition_payment', ['tuition_amount' => $tuition_amount, 'remaining_tuition' => $remaining_tuition, 'Student' => $Student])->render();
    }

    public function tuition_payment_process (Request $request)
    {
        if ($request->payment < 1)
        {
            return json_encode(['code' => 2, 'general_message' => 'Invalid payment.']);
        }

        $Student = Student::with([
                                    'grade', 
                                    'section', 
                                    'tuition' => function ($query) {
                                        $query->where('status', 1);
                                    },
                                    'grade.tuition_fee' => function ($query) {
                                        $query->where('status', 1);
                                    },
                                ])
                                ->where('status', 1)
                                ->where('id', $request->id)
                                ->first();
        
        $StudentTuitionFee = StudentTuitionFee::where('student_id', $request->id)->first();

        if ($StudentTuitionFee->total_remaining <= 0)
        {
            return json_encode(['code' => 2, 'general_message' => 'Already paid.']);
        }

        $tuition_amount = 0 ;
        
        $down_payment_amount = $Student->grade->tuition_fee[0]->misc_fee + 2000;

        $down_payment_amount_balance = $request->payment - $down_payment_amount;
        
        $remaining_payment = $request->payment;
        // $StudentTuitionFee->down_payment    = 1000;
        // $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - 1000;
        if ($StudentTuitionFee->down_payment == 0)
        {
            if ($down_payment_amount_balance > 0) // if down payment is greater to down payment amount
            {
                $StudentTuitionFee->down_payment = $down_payment_amount;
                $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $down_payment_amount;
                $remaining_payment = $down_payment_amount_balance;
            }
            else // down payment is less than to down payment amount
            {
                $StudentTuitionFee->down_payment = $request->payment;
                $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $request->payment;
                $remaining_payment = 0;
            }
        }

        // $remaining_payment = $down_payment_amount - $request->payment;
        // $remaining_tuition = $Student->tuition[0]->total_remaining;
        
        $monthly_amount = 0;
        if ($StudentTuitionFee->monthly_payment == 0)
        {
            $monthly_amount = $StudentTuitionFee->total_remaining / 10;
            $StudentTuitionFee->monthly_payment = $monthly_amount;
            
            $total_discount = $StudentTuitionFee->total_discount;

            // discount - 10th month
            if ($total_discount > 0)
            {
                if ($total_discount > $monthly_amount)
                {
                    $StudentTuitionFee->month_10_payment = $monthly_amount;
                    $total_discount -= $monthly_amount;
                    $StudentTuitionFee->total_remaining -= $monthly_amount;
                }
                else
                {
                    $StudentTuitionFee->month_10_payment = $total_discount;
                    $total_discount = 0;
                    $StudentTuitionFee->total_remaining -= $total_discount;
                }
            }

            // discount - 9th month
            if ($total_discount > 0)
            {
                if ($total_discount > $monthly_amount)
                {
                    $StudentTuitionFee->month_9_payment = $monthly_amount;
                    $total_discount -= $monthly_amount;
                    $StudentTuitionFee->total_remaining -= $monthly_amount;
                }
                else
                {
                    $StudentTuitionFee->month_9_payment = $total_discount;
                    $total_discount = 0;
                    $StudentTuitionFee->total_remaining -= $total_discount;
                }
            }
            
            // discount - 8th month
            if ($total_discount > 0)
            {
                if ($total_discount > $monthly_amount)
                {
                    $StudentTuitionFee->month_8_payment = $monthly_amount;
                    $total_discount -= $monthly_amount;
                    $StudentTuitionFee->total_remaining -= $monthly_amount;
                }
                else
                {
                    $StudentTuitionFee->month_8_payment = $total_discount;
                    $total_discount = 0;
                    $StudentTuitionFee->total_remaining -= $total_discount;
                }
            }
            // discount - 7th month
            if ($total_discount > 0)
            {
                if ($total_discount > $monthly_amount)
                {
                    $StudentTuitionFee->month_7_payment = $monthly_amount;
                    $total_discount -= $monthly_amount;
                    $StudentTuitionFee->total_remaining -= $monthly_amount;
                }
                else
                {
                    $StudentTuitionFee->month_7_payment = $total_discount;
                    $total_discount = 0;
                    $StudentTuitionFee->total_remaining -= $total_discount;
                }
            }
            // discount - 6th month
            if ($total_discount > 0)
            {
                if ($total_discount > $monthly_amount)
                {
                    $StudentTuitionFee->month_6_payment = $monthly_amount;
                    $total_discount -= $monthly_amount;
                    $StudentTuitionFee->total_remaining -= $monthly_amount;
                }
                else
                {
                    $StudentTuitionFee->month_6_payment = $total_discount;
                    $total_discount = 0;
                    $StudentTuitionFee->total_remaining -= $total_discount;
                }
            }
            // discount - 5th month
            if ($total_discount > 0)
            {
                if ($total_discount > $monthly_amount)
                {
                    $StudentTuitionFee->month_5_payment = $monthly_amount;
                    $total_discount -= $monthly_amount;
                    $StudentTuitionFee->total_remaining -= $monthly_amount;
                }
                else
                {
                    $StudentTuitionFee->month_5_payment = $total_discount;
                    $total_discount = 0;
                    $StudentTuitionFee->total_remaining -= $total_discount;
                }
            }
            // discount - 4th month
            if ($total_discount > 0)
            {
                if ($total_discount > $monthly_amount)
                {
                    $StudentTuitionFee->month_4_payment = $monthly_amount;
                    $total_discount -= $monthly_amount;
                    $StudentTuitionFee->total_remaining -= $monthly_amount;
                }
                else
                {
                    $StudentTuitionFee->month_4_payment = $total_discount;
                    $total_discount = 0;
                    $StudentTuitionFee->total_remaining -= $total_discount;
                }
            }
            // discount - 3rd month
            if ($total_discount > 0)
            {
                if ($total_discount > $monthly_amount)
                {
                    $StudentTuitionFee->month_3_payment = $monthly_amount;
                    $total_discount -= $monthly_amount;
                    $StudentTuitionFee->total_remaining -= $monthly_amount;
                }
                else
                {
                    $StudentTuitionFee->month_3_payment = $total_discount;
                    $total_discount = 0;
                    $StudentTuitionFee->total_remaining -= $total_discount;
                }
            }
            // discount - 2nd month
            if ($total_discount > 0)
            {
                if ($total_discount > $monthly_amount)
                {
                    $StudentTuitionFee->month_2nd_payment = $monthly_amount;
                    $total_discount -= $monthly_amount;
                    $StudentTuitionFee->total_remaining -= $monthly_amount;
                }
                else
                {
                    $StudentTuitionFee->month_2nd_payment = $total_discount;
                    $total_discount = 0;
                    $StudentTuitionFee->total_remaining -= $total_discount;
                }
            }
            // discount - 1st month
            if ($total_discount > 0)
            {
                if ($total_discount > $monthly_amount)
                {
                    $StudentTuitionFee->month_1_payment = $monthly_amount;
                    $total_discount -= $monthly_amount;
                    $StudentTuitionFee->total_remaining -= $monthly_amount;
                }
                else
                {
                    $StudentTuitionFee->month_1_payment = $total_discount;
                    $total_discount = 0;
                    $StudentTuitionFee->total_remaining -= $total_discount;
                }
            }
        }
        else
        {
            $monthly_amount = $StudentTuitionFee->monthly_payment;
        }

        // 1st month
        if ($remaining_payment > 0)
        {
            if ($StudentTuitionFee->month_1_payment == 0 || $StudentTuitionFee->month_1_payment < $monthly_amount)
            { 

                    if ($StudentTuitionFee->month_1_payment <= 0)
                    {
                        if ($remaining_payment > $monthly_amount)
                        {
                            $StudentTuitionFee->month_1_payment += $monthly_amount;
                            $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $monthly_amount;
                            $remaining_payment = $remaining_payment - $monthly_amount;
                        }
                        else
                        {
                            $StudentTuitionFee->month_1_payment += $remaining_payment; 
                            $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
                            $remaining_payment = 0;
                        }
                    }
                    else
                    {
                        if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_1_payment))
                        {
                            $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_1_payment);
                            $StudentTuitionFee->month_1_payment += $to_be_deduct;
                            $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $to_be_deduct;
                            $remaining_payment = $remaining_payment - $to_be_deduct;
                        }
                        else
                        {
                            $StudentTuitionFee->month_1_payment += $remaining_payment; 
                            $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
                            $remaining_payment = 0;
                        }
                    }
                
            }
        }
        // return json_encode(['code' => 0 ,'general_message' => 'Payment success', 'remaining_payment' => $remaining_payment, 'StudentTuitionFee' => $StudentTuitionFee]);
        // 2nd month
        if ($remaining_payment > 0)
        {
            if ($StudentTuitionFee->month_2_payment == 0 || $StudentTuitionFee->month_2_payment < $monthly_amount)
            { 
                if ($StudentTuitionFee->month_2_payment <= 0)
                {
                    if ($remaining_payment >= $monthly_amount)
                    {
                        $StudentTuitionFee->month_2_payment += $monthly_amount;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $monthly_amount;
                        $remaining_payment = $remaining_payment - $monthly_amount;
                    }
                    else
                    {
                        $StudentTuitionFee->month_2_payment += $remaining_payment;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
                        $remaining_payment = 0;
                    }
                }
                else
                {
                    if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_2_payment))
                    {
                        $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_2_payment);
                        $StudentTuitionFee->month_2_payment += $to_be_deduct;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $to_be_deduct;
                        $remaining_payment = $remaining_payment - $to_be_deduct;
                    }
                    else
                    {
                        $StudentTuitionFee->month_2_payment += $remaining_payment; 
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
                        $remaining_payment = 0;
                    }
                }
            }
        }
        // 3nd month
        if ($remaining_payment > 0)
        {
            if ($StudentTuitionFee->month_3_payment == 0 || $StudentTuitionFee->month_3_payment < $monthly_amount)
            { 
                if ($StudentTuitionFee->month_3_payment <= 0)
                {
                    if ($remaining_payment >= $monthly_amount)
                    {
                        $StudentTuitionFee->month_3_payment += $monthly_amount;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $monthly_amount;
                        $remaining_payment = $remaining_payment - $monthly_amount;
                    }
                    else
                    {
                        $StudentTuitionFee->month_3_payment += $remaining_payment;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
                        $remaining_payment = 0;
                    }
                }
                else
                {
                    if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_3_payment))
                    {
                        $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_3_payment);
                        $StudentTuitionFee->month_3_payment += $to_be_deduct;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $to_be_deduct;
                        $remaining_payment = $remaining_payment - $to_be_deduct;
                    }
                    else
                    {
                        $StudentTuitionFee->month_3_payment += $remaining_payment; 
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
                        $remaining_payment = 0;
                    }
                }
            }
        }
        // 4th month
        if ($remaining_payment > 0)
        {
            if ($StudentTuitionFee->month_4_payment == 0 || $StudentTuitionFee->month_4_payment < $monthly_amount)
            { 
                if ($StudentTuitionFee->month_4_payment <= 0)
                {
                    if ($remaining_payment >= $monthly_amount)
                    {
                        $StudentTuitionFee->month_4_payment += $monthly_amount;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $monthly_amount;
                        $remaining_payment = $remaining_payment - $monthly_amount;
                    }
                    else
                    {
                        $StudentTuitionFee->month_4_payment += $remaining_payment;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
                        $remaining_payment = 0;
                    }
                }
                else
                {
                    if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_4_payment))
                    {
                        $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_4_payment);
                        $StudentTuitionFee->month_4_payment += $to_be_deduct;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $to_be_deduct;
                        $remaining_payment = $remaining_payment - $to_be_deduct;
                    }
                    else
                    {
                        $StudentTuitionFee->month_4_payment += $remaining_payment; 
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
                        $remaining_payment = 0;
                    }
                }
            }
        }
        // 5th month
        if ($remaining_payment > 0)
        {
            if ($StudentTuitionFee->month_5_payment == 0 || $StudentTuitionFee->month_5_payment < $monthly_amount)
            { 
                if ($StudentTuitionFee->month_5_payment <= 0)
                {
                    if ($remaining_payment >= $monthly_amount)
                    {
                        $StudentTuitionFee->month_5_payment += $monthly_amount;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $monthly_amount;
                        $remaining_payment = $remaining_payment - $monthly_amount;
                    }
                    else
                    {
                        $StudentTuitionFee->month_5_payment += $remaining_payment;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
                        $remaining_payment = 0;
                    }
                }
                else
                {
                    if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_5_payment))
                    {
                        $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_5_payment);
                        $StudentTuitionFee->month_5_payment += $to_be_deduct;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $to_be_deduct;
                        $remaining_payment = $remaining_payment - $to_be_deduct;
                    }
                    else
                    {
                        $StudentTuitionFee->month_5_payment += $remaining_payment; 
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
                        $remaining_payment = 0;
                    }
                }
            }
        }
        // 6th month
        if ($remaining_payment > 0)
        {
            if ($StudentTuitionFee->month_6_payment == 0 || $StudentTuitionFee->month_6_payment < $monthly_amount)
            { 
                if ($StudentTuitionFee->month_6_payment <= 0)
                {
                    if ($remaining_payment >= $monthly_amount)
                    {
                        $StudentTuitionFee->month_6_payment += $monthly_amount;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $monthly_amount;
                        $remaining_payment = $remaining_payment - $monthly_amount;
                    }
                    else
                    {
                        $StudentTuitionFee->month_6_payment += $remaining_payment;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
                        $remaining_payment = 0;
                    }
                }
                else
                {
                    if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_6_payment))
                    {
                        $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_6_payment);
                        $StudentTuitionFee->month_6_payment += $to_be_deduct;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $to_be_deduct;
                        $remaining_payment = $remaining_payment - $to_be_deduct;
                    }
                    else
                    {
                        $StudentTuitionFee->month_6_payment += $remaining_payment; 
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
                        $remaining_payment = 0;
                    }
                }
            }
        }
        // 7th month
        if ($remaining_payment > 0)
        {
            if ($StudentTuitionFee->month_7_payment == 0 || $StudentTuitionFee->month_7_payment < $monthly_amount)
            { 
                if ($StudentTuitionFee->month_7_payment <= 0)
                {
                    if ($remaining_payment >= $monthly_amount)
                    {
                        $StudentTuitionFee->month_7_payment += $monthly_amount;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $monthly_amount;
                        $remaining_payment = $remaining_payment - $monthly_amount;
                    }
                    else
                    {
                        $StudentTuitionFee->month_7_payment += $remaining_payment;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
                        $remaining_payment = 0;
                    }
                }
                else
                {
                    if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_7_payment))
                    {
                        $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_7_payment);
                        $StudentTuitionFee->month_7_payment += $to_be_deduct;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $to_be_deduct;
                        $remaining_payment = $remaining_payment - $to_be_deduct;
                    }
                    else
                    {
                        $StudentTuitionFee->month_7_payment += $remaining_payment; 
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
                        $remaining_payment = 0;
                    }
                }
            }
        }
        // 8th month
        if ($remaining_payment > 0)
        {
            if ($StudentTuitionFee->month_8_payment == 0 || $StudentTuitionFee->month_8_payment < $monthly_amount)
            { 
                if ($StudentTuitionFee->month_8_payment <= 0)
                {
                    if ($remaining_payment >= $monthly_amount)
                    {
                        $StudentTuitionFee->month_8_payment += $monthly_amount;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $monthly_amount;
                        $remaining_payment = $remaining_payment - $monthly_amount;
                    }
                    else
                    {
                        $StudentTuitionFee->month_8_payment += $remaining_payment;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
                        $remaining_payment = 0;
                    }
                }
                else
                {
                    if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_8_payment))
                    {
                        $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_8_payment);
                        $StudentTuitionFee->month_8_payment += $to_be_deduct;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $to_be_deduct;
                        $remaining_payment = $remaining_payment - $to_be_deduct;
                    }
                    else
                    {
                        $StudentTuitionFee->month_8_payment += $remaining_payment; 
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
                        $remaining_payment = 0;
                    }
                }
            }
        }
        // 9th month
        if ($remaining_payment > 0)
        {
            if ($StudentTuitionFee->month_9_payment == 0 || $StudentTuitionFee->month_9_payment < $monthly_amount)
            { 
                if ($StudentTuitionFee->month_9_payment <= 0)
                {
                    if ($remaining_payment >= $monthly_amount)
                    {
                        $StudentTuitionFee->month_9_payment += $monthly_amount;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $monthly_amount;
                        $remaining_payment = $remaining_payment - $monthly_amount;
                    }
                    else
                    {
                        $StudentTuitionFee->month_9_payment += $remaining_payment;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
                        $remaining_payment = 0;
                    }
                }
                else
                {
                    if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_9_payment))
                    {
                        $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_9_payment);
                        $StudentTuitionFee->month_9_payment += $to_be_deduct;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $to_be_deduct;
                        $remaining_payment = $remaining_payment - $to_be_deduct;
                    }
                    else
                    {
                        $StudentTuitionFee->month_9_payment += $remaining_payment; 
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
                        $remaining_payment = 0;
                    }
                }
            }
        }
        // 10th month
        if ($remaining_payment > 0)
        {
            if ($StudentTuitionFee->month_10_payment == 0 || $StudentTuitionFee->month_10_payment < $monthly_amount)
            { 
                if ($StudentTuitionFee->month_10_payment <= 0)
                {
                    if ($remaining_payment >= $monthly_amount)
                    {
                        $StudentTuitionFee->month_10_payment += $monthly_amount;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $monthly_amount;
                        $remaining_payment = $remaining_payment - $monthly_amount;
                    }
                    else
                    {
                        $StudentTuitionFee->month_10_payment += $remaining_payment;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
                        $remaining_payment = 0;
                    }
                }
                else
                {
                    if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_10_payment))
                    {
                        $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_10_payment);
                        $StudentTuitionFee->month_10_payment += $to_be_deduct;
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $to_be_deduct;
                        $remaining_payment = $remaining_payment - $to_be_deduct;
                    }
                    else
                    {
                        $StudentTuitionFee->month_10_payment += $remaining_payment; 
                        $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
                        $remaining_payment = 0;
                    }
                }
            }
        }

        if ($StudentTuitionFee->total_remaining <= 0)
        {
            $StudentTuitionFee->total_remaining = 0;
            $StudentTuitionFee->fully_paid = 1;
        }

        $StudentTuitionFee->save();

        $StudentPaymentLog = new StudentPaymentLog();
        $StudentPaymentLog->student_id  = $request->id;
        $StudentPaymentLog->payment     = $request->payment;
        $StudentPaymentLog->payment_type= 2;
        $StudentPaymentLog->received_by = 1;
        $StudentPaymentLog->save();

        return json_encode(['code' => 0 ,'general_message' => 'Payment success', 'remaining_payment' => $remaining_payment, 'StudentTuitionFee' => $StudentTuitionFee]);
    }

    public function student_summary_balance (Request $request)
    {


        $Students = Student::with(['grade', 
                                    'section', 
                                    'tuition' => function ($query) {
                                        $query->where('status', 1);
                                    }
                                ])
                                ->where(function ($query) use ($request){
                                    $query->whereRaw("concat(first_name, ' ', middle_name , ' ', last_name) like '%". $request->search_filter ."%' ");

                                    if ($request->filter_grade)
                                    {
                                        $query->where('grade_id', $request->filter_grade);
                                    }

                                    if ($request->filter_section)
                                    {
                                        $query->where('section_id', $request->filter_section);
                                    }
                                })
                                ->where('status', 1)
                                ->get();
        $StudentTuitionFee = StudentTuitionFee::selectRaw('sum(total_remaining) as total_tuition_balance, sum(additional_fee) as total_additional_fee')
                                    ->first();
        // return json_encode($Students);
        // PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        $pdf = PDF::loadView('cashier.student_payment.report.pdf_student_summary_balance', ['Students' => $Students, 'StudentTuitionFee' => $StudentTuitionFee]);
        return $pdf->stream();
        // return $pdf->download('Balance-Summary-List.pdf');

        return view('cashier.student_payment.report.pdf_student_summary_balance', ['Students' => $Students, 'StudentTuitionFee' => $StudentTuitionFee]);
    }
}
