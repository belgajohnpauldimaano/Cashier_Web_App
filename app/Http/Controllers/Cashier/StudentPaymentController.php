<?php

namespace App\Http\Controllers\Cashier;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use Carbon;
use DB;
use Auth;

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
                                    },
                                    'discount_list',
                                    'grade_tuition',
                                    'additional_fee'
                                ])
                                ->where('status', 1)
                                ->orderBy('grade_id', 'ASC')
                                ->paginate(10);
        $Student_tuition = Student::with(['grade_tuition' => function ($query) {
                                            $query->select(['grade_id', 'tuition_fee']);
                                        }
                                    ])
                                    ->get();
        $Grade = Grade::all();
        $Section = Section::where('grade_id', 1)->get();
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
                                    },
                                    'discount_list',
                                    'grade_tuition',
                                    'additional_fee'
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
                                ->orderBy('grade_id', 'ASC')
                                ->paginate($pages);
        // return json_encode(['Students' => $Students, 'request' => $request->all()]);
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
                                    'additional_fee'
                                ])
                                ->where('status', 1)
                                ->where('id', $request->id)
                                ->first();
        
        $total_additional_fee = 0;
        if ($Student->additional_fee)
        {
            foreach($Student->additional_fee as $additionl_fee)
            {
                $total_additional_fee += $additionl_fee->additional_amount;
            }
        }

        if ($Student->tuition)
        {
            $total_additional_fee -= $Student->tuition[0]->additional_fee_total;
        }

        // $StudentTuitionFee = StudentTuitionFee::where('student_id', $request->id)->first();


        
        return view('cashier.student_payment.partials.form_modal_additional_payment', ['total_additional_fee' => $total_additional_fee, 'student_id' => $Student->id, 'student_tuition' => $Student->tuition])->render();
    }

    public function additional_fee_payment_process (Request $request)
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
                                    'additional_fee'
                                ])
                                ->where('status', 1)
                                ->where('id', $request->id)
                                ->first();
        $total_additional_fee = 0;
        if ($Student->additional_fee)
        {
            foreach($Student->additional_fee as $additionl_fee)
            {
                $total_additional_fee += $additionl_fee->additional_amount;
            }
        }

        if ($Student->tuition)
        {
            $total_additional_fee -= $Student->tuition[0]->additional_fee_total;
        }


        if ($total_additional_fee < $request->payment)
        {
            return json_encode(['code' => 0 ,'general_message' => 'Your payment is too large for the balance.']);
        }

        $StudentTuitionFee = StudentTuitionFee::where('student_id', $request->id)->first();
        $StudentTuitionFee->additional_fee_total += $request->payment;
        $StudentTuitionFee->save();

        $StudentPaymentLog = new StudentPaymentLog();
        $StudentPaymentLog->student_id  = $request->id;
        $StudentPaymentLog->payment     = $request->payment;
        $StudentPaymentLog->payment_type= 2;
        $StudentPaymentLog->received_by = Auth::user()->id;
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
                                    'discount_list',
                                    'grade_tuition',
                                    'additional_fee'
                                ])
                                ->where('status', 1)
                                ->where('id', $request->id)
                                ->first();
        $discount = 0;
        $tuition = $Student->grade_tuition[0]->tuition_fee; 
        $misc_fee = $Student->grade_tuition[0]->misc_fee;
        $total_tuition = $tuition + $misc_fee; 

        $discount += ($Student->discount_list->scholar != 0 ? $Student->discount_list->scholar * $tuition : 0);
        $discount += ($Student->discount_list->school_subsidy != 0 ? $Student->discount_list->school_subsidy : 0);
        $discount += ($Student->discount_list->employee_scholar != 0 ? $Student->discount_list->employee_scholar * $tuition : 0);
        $discount += ($Student->discount_list->gov_subsidy  != 0 ? $Student->discount_list->gov_subsidy  : 0);
        $discount += ($Student->discount_list->acad_scholar  != 0 ? $Student->discount_list->acad_scholar * $tuition : 0);
        $discount += ($Student->discount_list->family_member  != 0 ? $Student->discount_list->family_member * $tuition : 0);
        $discount += ($Student->discount_list->nbi_alumni  != 0 ? $Student->discount_list->nbi_alumni * $tuition : 0);
        $discount += ($Student->discount_list->cash_discount  != 0 ? $Student->discount_list->cash_discount * $tuition : 0);
        $discount += ($Student->discount_list->cwoir_discount  != 0 ? $Student->discount_list->cwoir_discount * $tuition : 0);
        $discount += ($Student->discount_list->st_joseph_discount  != 0 ? $Student->discount_list->st_joseph_discount : 0);
        

        // payment made
        $total_tuition_payment = $Student->tuition[0]->total_payment;
        $down_payment = $Student->tuition[0]->down_payment;

        $net_tuition = $total_tuition - $discount;
        $net_tuition_no_discount = $total_tuition - $total_tuition_payment;
        $outstanding_balance = $net_tuition - $total_tuition_payment;

        $monthly = $misc_fee + 2000;

        if ($down_payment < $monthly)
        // if ($down_payment == 0)
        {
            $monthly = $monthly - $down_payment;
            if ($monthly > $net_tuition && $down_payment == 0)
            {
                $monthly = $net_tuition;
            }
        }
        else
        {
            // $monthly = ($net_tuition - $down_payment) / 10;
            $monthly = ($total_tuition - $down_payment) / 10;
        }

        if ($monthly > $outstanding_balance)
        {
            $monthly = $outstanding_balance;
        }
        // echo $monthly.  '=' . $net_tuition ."-". $down_payment . " / 10";
        // return json_encode(['student_id' => $Student->id, 'outstanding_balance' => $outstanding_balance, 'monthly' => $monthly]);
        
        return view('cashier.student_payment.partials.form_modal_tuition_payment', ['student_id' => $Student->id, 'outstanding_balance' => $outstanding_balance, 'misc_fee' => $misc_fee, 'tuition' => $tuition , 'total_tuition' => $total_tuition, 'monthly' => $monthly, 'net_tuition_no_discount' => $net_tuition_no_discount, 'total_tuition_payment' => $total_tuition_payment, 'discount' => $discount])->render();
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

        $tuition_amount_updated = $Student->grade->tuition_fee[0]->tuition_fee + $Student->grade->tuition_fee[0]->misc_fee;
        echo $tuition_amount_updated.  ' ' .  $StudentTuitionFee->total_tuition;
        if ($tuition_amount_updated != $StudentTuitionFee->total_tuition) // there is a changes on grade level tuition
        {
            if ($StudentTuitionFee->total_payment > 0)
            {

                $new_dp = $Student->grade->tuition_fee[0]->misc_fee + 2000;
                $new_tuition = $tuition_amount_updated;
                $new_monthly_payment = 0;
                // $old_total_payment  = $StudentTuitionFee->total_tuition;
                $old_total_payment      = $StudentTuitionFee->total_payment;
                $old_dp                 = $StudentTuitionFee->down_payment;
                $old_total_tuition      = $StudentTuitionFee->total_tuition;

                $dp = 0;
                if ($new_dp > $old_dp)
                {
                    $dp = $old_dp;
                    $StudentTuitionFee->down_payment = $old_dp;
                    $old_total_payment -= $old_dp;
                }
                else if ($new_dp < $old_dp)
                {
                    $dp = $new_dp;
                    $StudentTuitionFee->down_payment = $new_dp;
                    $old_total_payment -= $new_dp;
                }

                $new_remaining_tuition_payment -= $new_tuition - $dp;
                $new_monthly_payment = $new_remaining_tuition_payment  / 10;

                if ($new_remaining_tuition_payment >= $new_monthly_payment) // check if there is a remaining if we deduct the remaining to monthly payment
                {
                    $StudentTuitionFee->month_1_payment = $new_monthly_payment;
                    $new_remaining_tuition_payment -= $new_monthly_payment;
                }
                else if ($new_remaining_tuition_payment != 0)
                {
                    $StudentTuitionFee->month_1_payment = $new_remaining_tuition_payment;
                    $new_remaining_tuition_payment -= $new_monthly_payment;
                }
            
            }
            else // no payment was added yet
            {
                echo "fsdfa";
            }


        }
        // echo "no changes";
        return;

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
        
        $Validator = Validator::make($request->all(), [
                                        'payment' => 'required|digits_between:3,6',
                                        'or_number' => 'required',
                                        'date_received' => 'required|date_format:Y-m-d'
                                    ], [
                                        
                                    ]);
        if ($Validator->fails())
        {
            return json_encode(['code' => 1, 'general_message' => 'Fill all required fields.', 'messages' => $Validator->getMessageBag()]);
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
                                    'discount_list',
                                    'grade_tuition',
                                    'additional_fee'
                                ])
                                ->where('status', 1)
                                ->where('id', $request->id)
                                ->first();
        
        $StudentTuitionFee = StudentTuitionFee::where('student_id', $request->id)->first();

        if (!$Student)
        {

        }

        $discount = 0;
        $tuition = $Student->grade_tuition[0]->tuition_fee; 
        $misc_fee = $Student->grade_tuition[0]->misc_fee;
        $total_tuition = $tuition + $misc_fee; 

        $discount += ($Student->discount_list->scholar != 0 ? $Student->discount_list->scholar * $tuition : 0);
        $discount += ($Student->discount_list->school_subsidy != 0 ? $Student->discount_list->school_subsidy : 0);
        $discount += ($Student->discount_list->employee_scholar != 0 ? $Student->discount_list->employee_scholar * $tuition : 0);
        $discount += ($Student->discount_list->gov_subsidy  != 0 ? $Student->discount_list->gov_subsidy  : 0);
        $discount += ($Student->discount_list->acad_scholar  != 0 ? $Student->discount_list->acad_scholar * $tuition : 0);
        $discount += ($Student->discount_list->family_member  != 0 ? $Student->discount_list->family_member * $tuition : 0);
        $discount += ($Student->discount_list->nbi_alumni  != 0 ? $Student->discount_list->nbi_alumni * $tuition : 0);
        $discount += ($Student->discount_list->cash_discount  != 0 ? $Student->discount_list->cash_discount * $tuition : 0);
        $discount += ($Student->discount_list->cwoir_discount  != 0 ? $Student->discount_list->cwoir_discount * $tuition : 0);
        $discount += ($Student->discount_list->st_joseph_discount  != 0 ? $Student->discount_list->st_joseph_discount : 0);
        
        $StudentTuitionFee->total_payment += $request->payment;

        $remaining_payment = $request->payment;

        

        // payment made
        $total_tuition_payment = $Student->tuition[0]->total_payment;
        $down_payment = $Student->tuition[0]->down_payment;

        $net_tuition = $total_tuition - $discount;
        $outstanding_balance = $net_tuition - $total_tuition_payment;

        if ($StudentTuitionFee->total_payment > $net_tuition)
        {
            return json_encode(['code' => 1, 'general_message' => 'Payment too large.']);
        }

        $monthly_amount = $misc_fee + 2000;

        // if ($down_payment == 0)
        if ($down_payment <= $monthly_amount)
        {
            if ($monthly_amount > $net_tuition)
            {
                $monthly_amount = $net_tuition;
            }

            if ($StudentTuitionFee->total_payment > $net_tuition)
            {
                return json_encode(['code' => 1, 'general_message' => 'Payment too large.']);
            }
            
            $remaining_dp = $monthly_amount - $down_payment;

            // if ($monthly_amount > $remaining_payment) // if down payment is greater to down payment amount
            if ($remaining_dp > $remaining_payment)
            {

                $StudentTuitionFee->down_payment += $remaining_payment;
                // $remaining_payment -= $monthly_amount;
            }
            else // down payment is less than to down payment amount
            {
                $StudentTuitionFee->down_payment = $monthly_amount;
                // $remaining_payment = 0;
            }
        }
        else
        {
            $monthly_amount = ($total_tuition - ($misc_fee + 2000)) / 10;
            // $monthly_amount = ($net_tuition - $down_payment) / 10;
        }
        
        $monthly_amount = ($total_tuition - ($misc_fee + 2000)) / 10;
        $remaining_payment = $StudentTuitionFee->total_payment - $StudentTuitionFee->down_payment;

        if ($remaining_payment > 0)
        {
            if ($StudentTuitionFee->month_1_payment == 0 || $StudentTuitionFee->month_1_payment < $monthly_amount)
            { 

                    if ($StudentTuitionFee->month_1_payment <= 0)
                    {
                        if ($remaining_payment > $monthly_amount)
                        {
                            $StudentTuitionFee->month_1_payment += $monthly_amount;
                            $remaining_payment = $remaining_payment - $monthly_amount;
                        }
                        else
                        {
                            $StudentTuitionFee->month_1_payment += $remaining_payment; 
                            $remaining_payment = 0;
                        }
                    }
                    else
                    {
                        if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_1_payment))
                        {
                            $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_1_payment);
                            $StudentTuitionFee->month_1_payment += $to_be_deduct;
                            $remaining_payment = $remaining_payment - $to_be_deduct;
                        }
                        else
                        {
                            $StudentTuitionFee->month_1_payment += $remaining_payment; 
                            $remaining_payment = 0;
                        }
                    }
                
            }
        }

        if ($remaining_payment > 0)
        {
            if ($StudentTuitionFee->month_2_payment == 0 || $StudentTuitionFee->month_2_payment < $monthly_amount)
            { 

                    if ($StudentTuitionFee->month_2_payment <= 0)
                    {
                        if ($remaining_payment > $monthly_amount)
                        {
                            $StudentTuitionFee->month_2_payment += $monthly_amount;
                            $remaining_payment = $remaining_payment - $monthly_amount;
                        }
                        else
                        {
                            $StudentTuitionFee->month_2_payment += $remaining_payment; 
                            $remaining_payment = 0;
                        }
                    }
                    else
                    {
                        if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_2_payment))
                        {
                            $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_2_payment);
                            $StudentTuitionFee->month_2_payment += $to_be_deduct;
                            $remaining_payment = $remaining_payment - $to_be_deduct;
                        }
                        else
                        {
                            $StudentTuitionFee->month_2_payment += $remaining_payment; 
                            $remaining_payment = 0;
                        }
                    }
                
            }
        }
        
        
        if ($remaining_payment > 0)
        {
            

            if ($StudentTuitionFee->month_3_payment == 0 || $StudentTuitionFee->month_3_payment < $monthly_amount)
            { 

                    if ($StudentTuitionFee->month_3_payment <= 0)
                    {
                        if ($remaining_payment > $monthly_amount)
                        {
                            $StudentTuitionFee->month_3_payment += $monthly_amount;
                            $remaining_payment = $remaining_payment - $monthly_amount;
                        }
                        else
                        {
                            $StudentTuitionFee->month_3_payment += $remaining_payment; 
                            $remaining_payment = 0;
                        }
                    }
                    else
                    {
                        if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_3_payment))
                        {
                            $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_3_payment);
                            $StudentTuitionFee->month_3_payment += $to_be_deduct;
                            $remaining_payment = $remaining_payment - $to_be_deduct;
                        }
                        else
                        {
                            $StudentTuitionFee->month_3_payment += $remaining_payment; 
                            $remaining_payment = 0;
                        }
                    }
                
            }
        }

        
        if ($remaining_payment > 0)
        {
            if ($StudentTuitionFee->month_4_payment == 0 || $StudentTuitionFee->month_4_payment < $monthly_amount)
            { 

                    if ($StudentTuitionFee->month_4_payment <= 0)
                    {
                        if ($remaining_payment > $monthly_amount)
                        {
                            $StudentTuitionFee->month_4_payment += $monthly_amount;
                            $remaining_payment = $remaining_payment - $monthly_amount;
                        }
                        else
                        {
                            $StudentTuitionFee->month_4_payment += $remaining_payment; 
                            $remaining_payment = 0;
                        }
                    }
                    else
                    {
                        if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_4_payment))
                        {
                            $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_4_payment);
                            $StudentTuitionFee->month_4_payment += $to_be_deduct;
                            $remaining_payment = $remaining_payment - $to_be_deduct;
                        }
                        else
                        {
                            $StudentTuitionFee->month_4_payment += $remaining_payment; 
                            $remaining_payment = 0;
                        }
                    }
                
            }
        }
        
        if ($remaining_payment > 0)
        {
            if ($StudentTuitionFee->month_5_payment == 0 || $StudentTuitionFee->month_5_payment < $monthly_amount)
            { 

                    if ($StudentTuitionFee->month_5_payment <= 0)
                    {
                        if ($remaining_payment > $monthly_amount)
                        {
                            $StudentTuitionFee->month_5_payment += $monthly_amount;
                            $remaining_payment = $remaining_payment - $monthly_amount;
                        }
                        else
                        {
                            $StudentTuitionFee->month_5_payment += $remaining_payment; 
                            $remaining_payment = 0;
                        }
                    }
                    else
                    {
                        if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_5_payment))
                        {
                            $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_5_payment);
                            $StudentTuitionFee->month_5_payment += $to_be_deduct;
                            $remaining_payment = $remaining_payment - $to_be_deduct;
                        }
                        else
                        {
                            $StudentTuitionFee->month_5_payment += $remaining_payment; 
                            $remaining_payment = 0;
                        }
                    }
                
            }
        }
        
        if ($remaining_payment > 0)
        {
            if ($StudentTuitionFee->month_6_payment == 0 || $StudentTuitionFee->month_6_payment < $monthly_amount)
            { 

                    if ($StudentTuitionFee->month_6_payment <= 0)
                    {
                        if ($remaining_payment > $monthly_amount)
                        {
                            $StudentTuitionFee->month_6_payment += $monthly_amount;
                            $remaining_payment = $remaining_payment - $monthly_amount;
                        }
                        else
                        {
                            $StudentTuitionFee->month_6_payment += $remaining_payment; 
                            $remaining_payment = 0;
                        }
                    }
                    else
                    {
                        if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_6_payment))
                        {
                            $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_6_payment);
                            $StudentTuitionFee->month_6_payment += $to_be_deduct;
                            $remaining_payment = $remaining_payment - $to_be_deduct;
                        }
                        else
                        {
                            $StudentTuitionFee->month_6_payment += $remaining_payment; 
                            $remaining_payment = 0;
                        }
                    }
                
            }
        }
        
        if ($remaining_payment > 0)
        {
            if ($StudentTuitionFee->month_7_payment == 0 || $StudentTuitionFee->month_7_payment < $monthly_amount)
            { 

                    if ($StudentTuitionFee->month_7_payment <= 0)
                    {
                        if ($remaining_payment > $monthly_amount)
                        {
                            $StudentTuitionFee->month_7_payment += $monthly_amount;
                            $remaining_payment = $remaining_payment - $monthly_amount;
                        }
                        else
                        {
                            $StudentTuitionFee->month_7_payment += $remaining_payment; 
                            $remaining_payment = 0;
                        }
                    }
                    else
                    {
                        if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_7_payment))
                        {
                            $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_7_payment);
                            $StudentTuitionFee->month_7_payment += $to_be_deduct;
                            $remaining_payment = $remaining_payment - $to_be_deduct;
                        }
                        else
                        {
                            $StudentTuitionFee->month_7_payment += $remaining_payment; 
                            $remaining_payment = 0;
                        }
                    }
                
            }
        }
        
        if ($remaining_payment > 0)
        {
            if ($StudentTuitionFee->month_8_payment == 0 || $StudentTuitionFee->month_8_payment < $monthly_amount)
            { 

                    if ($StudentTuitionFee->month_8_payment <= 0)
                    {
                        if ($remaining_payment > $monthly_amount)
                        {
                            $StudentTuitionFee->month_8_payment += $monthly_amount;
                            $remaining_payment = $remaining_payment - $monthly_amount;
                        }
                        else
                        {
                            $StudentTuitionFee->month_8_payment += $remaining_payment; 
                            $remaining_payment = 0;
                        }
                    }
                    else
                    {
                        if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_8_payment))
                        {
                            $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_8_payment);
                            $StudentTuitionFee->month_8_payment += $to_be_deduct;
                            $remaining_payment = $remaining_payment - $to_be_deduct;
                        }
                        else
                        {
                            $StudentTuitionFee->month_8_payment += $remaining_payment; 
                            $remaining_payment = 0;
                        }
                    }
                
            }
        }
        
        if ($remaining_payment > 0)
        {
            if ($StudentTuitionFee->month_9_payment == 0 || $StudentTuitionFee->month_9_payment < $monthly_amount)
            { 

                    if ($StudentTuitionFee->month_9_payment <= 0)
                    {
                        if ($remaining_payment > $monthly_amount)
                        {
                            $StudentTuitionFee->month_9_payment += $monthly_amount;
                            $remaining_payment = $remaining_payment - $monthly_amount;
                        }
                        else
                        {
                            $StudentTuitionFee->month_9_payment += $remaining_payment; 
                            $remaining_payment = 0;
                        }
                    }
                    else
                    {
                        if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_9_payment))
                        {
                            $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_9_payment);
                            $StudentTuitionFee->month_9_payment += $to_be_deduct;
                            $remaining_payment = $remaining_payment - $to_be_deduct;
                        }
                        else
                        {
                            $StudentTuitionFee->month_9_payment += $remaining_payment; 
                            $remaining_payment = 0;
                        }
                    }
                
            }
        }
        
        if ($remaining_payment > 0)
        {
            if ($StudentTuitionFee->month_10_payment == 0 || $StudentTuitionFee->month_10_payment < $monthly_amount)
            { 

                    if ($StudentTuitionFee->month_10_payment <= 0)
                    {
                        if ($remaining_payment > $monthly_amount)
                        {
                            $StudentTuitionFee->month_10_payment += $monthly_amount;
                            $remaining_payment = $remaining_payment - $monthly_amount;
                        }
                        else
                        {
                            $StudentTuitionFee->month_10_payment += $remaining_payment; 
                            $remaining_payment = 0;
                        }
                    }
                    else
                    {
                        if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_10_payment))
                        {
                            $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_10_payment);
                            $StudentTuitionFee->month_10_payment += $to_be_deduct;
                            $remaining_payment = $remaining_payment - $to_be_deduct;
                        }
                        else
                        {
                            $StudentTuitionFee->month_10_payment += $remaining_payment; 
                            $remaining_payment = 0;
                        }
                    }
                
            }
        }
        
        $StudentTuitionFee->save();

        $StudentPaymentLog = new StudentPaymentLog();
        $StudentPaymentLog->student_id  = $request->id;
        $StudentPaymentLog->payment     = $request->payment;
        $StudentPaymentLog->payment_type= 1;
        $StudentPaymentLog->received_date= \Carbon\Carbon::parse($request->date_received)->format('Y-m-d H:i:s');
        $StudentPaymentLog->or_number= $request->or_number;
        $StudentPaymentLog->received_by = Auth::user()->id;
        $StudentPaymentLog->save();

        return json_encode(['code' => 0 ,'general_message' => 'Payment success', 'remaining_payment' => $remaining_payment, 'StudentTuitionFee' => $StudentTuitionFee]);

        return json_encode(['StudentTuitionFee' => $StudentTuitionFee, 'monthly_amount' => $monthly_amount, 'remaining_payment' => $remaining_payment]);

        // return json_encode(['Student' => $Student]);
        // $Student = Student::with([
        //                             'grade', 
        //                             'section', 
        //                             'tuition' => function ($query) {
        //                                 $query->where('status', 1);
        //                             },
        //                             'grade.tuition_fee' => function ($query) {
        //                                 $query->where('status', 1);
        //                             },
        //                         ])
        //                         ->where('status', 1)
        //                         ->where('id', $request->id)
        //                         ->first();
        
        // $StudentTuitionFee = StudentTuitionFee::where('student_id', $request->id)->first();

        // if ($StudentTuitionFee->total_remaining <= 0)
        // {
        //     return json_encode(['code' => 2, 'general_message' => 'Already paid.']);
        // }

        // $tuition_amount = 0;
        
        // $down_payment_amount = $Student->grade->tuition_fee[0]->misc_fee + 2000;

        // $down_payment_amount_balance = $request->payment - $down_payment_amount;
        
        // $remaining_payment = $request->payment;
        // // $StudentTuitionFee->down_payment    = 1000;
        // // $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - 1000;
        // if ($StudentTuitionFee->down_payment == 0)
        // {
        //     if ($down_payment_amount_balance > 0) // if down payment is greater to down payment amount
        //     {
        //         $StudentTuitionFee->down_payment = $down_payment_amount;
        //         $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $down_payment_amount;
        //         $remaining_payment = $down_payment_amount_balance;
        //     }
        //     else // down payment is less than to down payment amount
        //     {
        //         $StudentTuitionFee->down_payment = $request->payment;
        //         $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $request->payment;
        //         $remaining_payment = 0;
        //     }
        // }

        // // $remaining_payment = $down_payment_amount - $request->payment;
        // // $remaining_tuition = $Student->tuition[0]->total_remaining;
        
        // $monthly_amount = 0;
        // if ($StudentTuitionFee->monthly_payment == 0)
        // {
        //     $monthly_amount = $StudentTuitionFee->total_remaining / 10;
        //     $StudentTuitionFee->monthly_payment = $monthly_amount;
            
        //     $total_discount = $StudentTuitionFee->total_discount;

        //     // discount - 10th month
        //     if ($total_discount > 0)
        //     {
        //         if ($total_discount > $monthly_amount)
        //         {
        //             $StudentTuitionFee->month_10_payment = $monthly_amount;
        //             $total_discount -= $monthly_amount;
        //             $StudentTuitionFee->total_remaining -= $monthly_amount;
        //         }
        //         else
        //         {
        //             $StudentTuitionFee->month_10_payment = $total_discount;
        //             $total_discount = 0;
        //             $StudentTuitionFee->total_remaining -= $total_discount;
        //         }
        //     }

        //     // discount - 9th month
        //     if ($total_discount > 0)
        //     {
        //         if ($total_discount > $monthly_amount)
        //         {
        //             $StudentTuitionFee->month_9_payment = $monthly_amount;
        //             $total_discount -= $monthly_amount;
        //             $StudentTuitionFee->total_remaining -= $monthly_amount;
        //         }
        //         else
        //         {
        //             $StudentTuitionFee->month_9_payment = $total_discount;
        //             $total_discount = 0;
        //             $StudentTuitionFee->total_remaining -= $total_discount;
        //         }
        //     }
            
        //     // discount - 8th month
        //     if ($total_discount > 0)
        //     {
        //         if ($total_discount > $monthly_amount)
        //         {
        //             $StudentTuitionFee->month_8_payment = $monthly_amount;
        //             $total_discount -= $monthly_amount;
        //             $StudentTuitionFee->total_remaining -= $monthly_amount;
        //         }
        //         else
        //         {
        //             $StudentTuitionFee->month_8_payment = $total_discount;
        //             $total_discount = 0;
        //             $StudentTuitionFee->total_remaining -= $total_discount;
        //         }
        //     }
        //     // discount - 7th month
        //     if ($total_discount > 0)
        //     {
        //         if ($total_discount > $monthly_amount)
        //         {
        //             $StudentTuitionFee->month_7_payment = $monthly_amount;
        //             $total_discount -= $monthly_amount;
        //             $StudentTuitionFee->total_remaining -= $monthly_amount;
        //         }
        //         else
        //         {
        //             $StudentTuitionFee->month_7_payment = $total_discount;
        //             $total_discount = 0;
        //             $StudentTuitionFee->total_remaining -= $total_discount;
        //         }
        //     }
        //     // discount - 6th month
        //     if ($total_discount > 0)
        //     {
        //         if ($total_discount > $monthly_amount)
        //         {
        //             $StudentTuitionFee->month_6_payment = $monthly_amount;
        //             $total_discount -= $monthly_amount;
        //             $StudentTuitionFee->total_remaining -= $monthly_amount;
        //         }
        //         else
        //         {
        //             $StudentTuitionFee->month_6_payment = $total_discount;
        //             $total_discount = 0;
        //             $StudentTuitionFee->total_remaining -= $total_discount;
        //         }
        //     }
        //     // discount - 5th month
        //     if ($total_discount > 0)
        //     {
        //         if ($total_discount > $monthly_amount)
        //         {
        //             $StudentTuitionFee->month_5_payment = $monthly_amount;
        //             $total_discount -= $monthly_amount;
        //             $StudentTuitionFee->total_remaining -= $monthly_amount;
        //         }
        //         else
        //         {
        //             $StudentTuitionFee->month_5_payment = $total_discount;
        //             $total_discount = 0;
        //             $StudentTuitionFee->total_remaining -= $total_discount;
        //         }
        //     }
        //     // discount - 4th month
        //     if ($total_discount > 0)
        //     {
        //         if ($total_discount > $monthly_amount)
        //         {
        //             $StudentTuitionFee->month_4_payment = $monthly_amount;
        //             $total_discount -= $monthly_amount;
        //             $StudentTuitionFee->total_remaining -= $monthly_amount;
        //         }
        //         else
        //         {
        //             $StudentTuitionFee->month_4_payment = $total_discount;
        //             $total_discount = 0;
        //             $StudentTuitionFee->total_remaining -= $total_discount;
        //         }
        //     }
        //     // discount - 3rd month
        //     if ($total_discount > 0)
        //     {
        //         if ($total_discount > $monthly_amount)
        //         {
        //             $StudentTuitionFee->month_3_payment = $monthly_amount;
        //             $total_discount -= $monthly_amount;
        //             $StudentTuitionFee->total_remaining -= $monthly_amount;
        //         }
        //         else
        //         {
        //             $StudentTuitionFee->month_3_payment = $total_discount;
        //             $total_discount = 0;
        //             $StudentTuitionFee->total_remaining -= $total_discount;
        //         }
        //     }
        //     // discount - 2nd month
        //     if ($total_discount > 0)
        //     {
        //         if ($total_discount > $monthly_amount)
        //         {
        //             $StudentTuitionFee->month_2nd_payment = $monthly_amount;
        //             $total_discount -= $monthly_amount;
        //             $StudentTuitionFee->total_remaining -= $monthly_amount;
        //         }
        //         else
        //         {
        //             $StudentTuitionFee->month_2nd_payment = $total_discount;
        //             $total_discount = 0;
        //             $StudentTuitionFee->total_remaining -= $total_discount;
        //         }
        //     }
        //     // discount - 1st month
        //     if ($total_discount > 0)
        //     {
        //         if ($total_discount > $monthly_amount)
        //         {
        //             $StudentTuitionFee->month_1_payment = $monthly_amount;
        //             $total_discount -= $monthly_amount;
        //             $StudentTuitionFee->total_remaining -= $monthly_amount;
        //         }
        //         else
        //         {
        //             $StudentTuitionFee->month_1_payment = $total_discount;
        //             $total_discount = 0;
        //             $StudentTuitionFee->total_remaining -= $total_discount;
        //         }
        //     }
        // }
        // else
        // {
        //     $monthly_amount = $StudentTuitionFee->monthly_payment;
        // }

        // // 1st month
        // if ($remaining_payment > 0)
        // {
        //     if ($StudentTuitionFee->month_1_payment == 0 || $StudentTuitionFee->month_1_payment < $monthly_amount)
        //     { 

        //             if ($StudentTuitionFee->month_1_payment <= 0)
        //             {
        //                 if ($remaining_payment > $monthly_amount)
        //                 {
        //                     $StudentTuitionFee->month_1_payment += $monthly_amount;
        //                     $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $monthly_amount;
        //                     $remaining_payment = $remaining_payment - $monthly_amount;
        //                 }
        //                 else
        //                 {
        //                     $StudentTuitionFee->month_1_payment += $remaining_payment; 
        //                     $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
        //                     $remaining_payment = 0;
        //                 }
        //             }
        //             else
        //             {
        //                 if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_1_payment))
        //                 {
        //                     $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_1_payment);
        //                     $StudentTuitionFee->month_1_payment += $to_be_deduct;
        //                     $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $to_be_deduct;
        //                     $remaining_payment = $remaining_payment - $to_be_deduct;
        //                 }
        //                 else
        //                 {
        //                     $StudentTuitionFee->month_1_payment += $remaining_payment; 
        //                     $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
        //                     $remaining_payment = 0;
        //                 }
        //             }
                
        //     }
        // }
        // // return json_encode(['code' => 0 ,'general_message' => 'Payment success', 'remaining_payment' => $remaining_payment, 'StudentTuitionFee' => $StudentTuitionFee]);
        // // 2nd month
        // if ($remaining_payment > 0)
        // {
        //     if ($StudentTuitionFee->month_2_payment == 0 || $StudentTuitionFee->month_2_payment < $monthly_amount)
        //     { 
        //         if ($StudentTuitionFee->month_2_payment <= 0)
        //         {
        //             if ($remaining_payment >= $monthly_amount)
        //             {
        //                 $StudentTuitionFee->month_2_payment += $monthly_amount;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $monthly_amount;
        //                 $remaining_payment = $remaining_payment - $monthly_amount;
        //             }
        //             else
        //             {
        //                 $StudentTuitionFee->month_2_payment += $remaining_payment;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
        //                 $remaining_payment = 0;
        //             }
        //         }
        //         else
        //         {
        //             if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_2_payment))
        //             {
        //                 $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_2_payment);
        //                 $StudentTuitionFee->month_2_payment += $to_be_deduct;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $to_be_deduct;
        //                 $remaining_payment = $remaining_payment - $to_be_deduct;
        //             }
        //             else
        //             {
        //                 $StudentTuitionFee->month_2_payment += $remaining_payment; 
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
        //                 $remaining_payment = 0;
        //             }
        //         }
        //     }
        // }
        // // 3nd month
        // if ($remaining_payment > 0)
        // {
        //     if ($StudentTuitionFee->month_3_payment == 0 || $StudentTuitionFee->month_3_payment < $monthly_amount)
        //     { 
        //         if ($StudentTuitionFee->month_3_payment <= 0)
        //         {
        //             if ($remaining_payment >= $monthly_amount)
        //             {
        //                 $StudentTuitionFee->month_3_payment += $monthly_amount;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $monthly_amount;
        //                 $remaining_payment = $remaining_payment - $monthly_amount;
        //             }
        //             else
        //             {
        //                 $StudentTuitionFee->month_3_payment += $remaining_payment;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
        //                 $remaining_payment = 0;
        //             }
        //         }
        //         else
        //         {
        //             if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_3_payment))
        //             {
        //                 $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_3_payment);
        //                 $StudentTuitionFee->month_3_payment += $to_be_deduct;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $to_be_deduct;
        //                 $remaining_payment = $remaining_payment - $to_be_deduct;
        //             }
        //             else
        //             {
        //                 $StudentTuitionFee->month_3_payment += $remaining_payment; 
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
        //                 $remaining_payment = 0;
        //             }
        //         }
        //     }
        // }
        // // 4th month
        // if ($remaining_payment > 0)
        // {
        //     if ($StudentTuitionFee->month_4_payment == 0 || $StudentTuitionFee->month_4_payment < $monthly_amount)
        //     { 
        //         if ($StudentTuitionFee->month_4_payment <= 0)
        //         {
        //             if ($remaining_payment >= $monthly_amount)
        //             {
        //                 $StudentTuitionFee->month_4_payment += $monthly_amount;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $monthly_amount;
        //                 $remaining_payment = $remaining_payment - $monthly_amount;
        //             }
        //             else
        //             {
        //                 $StudentTuitionFee->month_4_payment += $remaining_payment;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
        //                 $remaining_payment = 0;
        //             }
        //         }
        //         else
        //         {
        //             if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_4_payment))
        //             {
        //                 $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_4_payment);
        //                 $StudentTuitionFee->month_4_payment += $to_be_deduct;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $to_be_deduct;
        //                 $remaining_payment = $remaining_payment - $to_be_deduct;
        //             }
        //             else
        //             {
        //                 $StudentTuitionFee->month_4_payment += $remaining_payment; 
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
        //                 $remaining_payment = 0;
        //             }
        //         }
        //     }
        // }
        // // 5th month
        // if ($remaining_payment > 0)
        // {
        //     if ($StudentTuitionFee->month_5_payment == 0 || $StudentTuitionFee->month_5_payment < $monthly_amount)
        //     { 
        //         if ($StudentTuitionFee->month_5_payment <= 0)
        //         {
        //             if ($remaining_payment >= $monthly_amount)
        //             {
        //                 $StudentTuitionFee->month_5_payment += $monthly_amount;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $monthly_amount;
        //                 $remaining_payment = $remaining_payment - $monthly_amount;
        //             }
        //             else
        //             {
        //                 $StudentTuitionFee->month_5_payment += $remaining_payment;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
        //                 $remaining_payment = 0;
        //             }
        //         }
        //         else
        //         {
        //             if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_5_payment))
        //             {
        //                 $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_5_payment);
        //                 $StudentTuitionFee->month_5_payment += $to_be_deduct;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $to_be_deduct;
        //                 $remaining_payment = $remaining_payment - $to_be_deduct;
        //             }
        //             else
        //             {
        //                 $StudentTuitionFee->month_5_payment += $remaining_payment; 
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
        //                 $remaining_payment = 0;
        //             }
        //         }
        //     }
        // }
        // // 6th month
        // if ($remaining_payment > 0)
        // {
        //     if ($StudentTuitionFee->month_6_payment == 0 || $StudentTuitionFee->month_6_payment < $monthly_amount)
        //     { 
        //         if ($StudentTuitionFee->month_6_payment <= 0)
        //         {
        //             if ($remaining_payment >= $monthly_amount)
        //             {
        //                 $StudentTuitionFee->month_6_payment += $monthly_amount;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $monthly_amount;
        //                 $remaining_payment = $remaining_payment - $monthly_amount;
        //             }
        //             else
        //             {
        //                 $StudentTuitionFee->month_6_payment += $remaining_payment;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
        //                 $remaining_payment = 0;
        //             }
        //         }
        //         else
        //         {
        //             if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_6_payment))
        //             {
        //                 $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_6_payment);
        //                 $StudentTuitionFee->month_6_payment += $to_be_deduct;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $to_be_deduct;
        //                 $remaining_payment = $remaining_payment - $to_be_deduct;
        //             }
        //             else
        //             {
        //                 $StudentTuitionFee->month_6_payment += $remaining_payment; 
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
        //                 $remaining_payment = 0;
        //             }
        //         }
        //     }
        // }
        // // 7th month
        // if ($remaining_payment > 0)
        // {
        //     if ($StudentTuitionFee->month_7_payment == 0 || $StudentTuitionFee->month_7_payment < $monthly_amount)
        //     { 
        //         if ($StudentTuitionFee->month_7_payment <= 0)
        //         {
        //             if ($remaining_payment >= $monthly_amount)
        //             {
        //                 $StudentTuitionFee->month_7_payment += $monthly_amount;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $monthly_amount;
        //                 $remaining_payment = $remaining_payment - $monthly_amount;
        //             }
        //             else
        //             {
        //                 $StudentTuitionFee->month_7_payment += $remaining_payment;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
        //                 $remaining_payment = 0;
        //             }
        //         }
        //         else
        //         {
        //             if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_7_payment))
        //             {
        //                 $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_7_payment);
        //                 $StudentTuitionFee->month_7_payment += $to_be_deduct;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $to_be_deduct;
        //                 $remaining_payment = $remaining_payment - $to_be_deduct;
        //             }
        //             else
        //             {
        //                 $StudentTuitionFee->month_7_payment += $remaining_payment; 
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
        //                 $remaining_payment = 0;
        //             }
        //         }
        //     }
        // }
        // // 8th month
        // if ($remaining_payment > 0)
        // {
        //     if ($StudentTuitionFee->month_8_payment == 0 || $StudentTuitionFee->month_8_payment < $monthly_amount)
        //     { 
        //         if ($StudentTuitionFee->month_8_payment <= 0)
        //         {
        //             if ($remaining_payment >= $monthly_amount)
        //             {
        //                 $StudentTuitionFee->month_8_payment += $monthly_amount;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $monthly_amount;
        //                 $remaining_payment = $remaining_payment - $monthly_amount;
        //             }
        //             else
        //             {
        //                 $StudentTuitionFee->month_8_payment += $remaining_payment;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
        //                 $remaining_payment = 0;
        //             }
        //         }
        //         else
        //         {
        //             if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_8_payment))
        //             {
        //                 $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_8_payment);
        //                 $StudentTuitionFee->month_8_payment += $to_be_deduct;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $to_be_deduct;
        //                 $remaining_payment = $remaining_payment - $to_be_deduct;
        //             }
        //             else
        //             {
        //                 $StudentTuitionFee->month_8_payment += $remaining_payment; 
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
        //                 $remaining_payment = 0;
        //             }
        //         }
        //     }
        // }
        // // 9th month
        // if ($remaining_payment > 0)
        // {
        //     if ($StudentTuitionFee->month_9_payment == 0 || $StudentTuitionFee->month_9_payment < $monthly_amount)
        //     { 
        //         if ($StudentTuitionFee->month_9_payment <= 0)
        //         {
        //             if ($remaining_payment >= $monthly_amount)
        //             {
        //                 $StudentTuitionFee->month_9_payment += $monthly_amount;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $monthly_amount;
        //                 $remaining_payment = $remaining_payment - $monthly_amount;
        //             }
        //             else
        //             {
        //                 $StudentTuitionFee->month_9_payment += $remaining_payment;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
        //                 $remaining_payment = 0;
        //             }
        //         }
        //         else
        //         {
        //             if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_9_payment))
        //             {
        //                 $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_9_payment);
        //                 $StudentTuitionFee->month_9_payment += $to_be_deduct;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $to_be_deduct;
        //                 $remaining_payment = $remaining_payment - $to_be_deduct;
        //             }
        //             else
        //             {
        //                 $StudentTuitionFee->month_9_payment += $remaining_payment; 
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
        //                 $remaining_payment = 0;
        //             }
        //         }
        //     }
        // }
        // // 10th month
        // if ($remaining_payment > 0)
        // {
        //     if ($StudentTuitionFee->month_10_payment == 0 || $StudentTuitionFee->month_10_payment < $monthly_amount)
        //     { 
        //         if ($StudentTuitionFee->month_10_payment <= 0)
        //         {
        //             if ($remaining_payment >= $monthly_amount)
        //             {
        //                 $StudentTuitionFee->month_10_payment += $monthly_amount;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $monthly_amount;
        //                 $remaining_payment = $remaining_payment - $monthly_amount;
        //             }
        //             else
        //             {
        //                 $StudentTuitionFee->month_10_payment += $remaining_payment;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
        //                 $remaining_payment = 0;
        //             }
        //         }
        //         else
        //         {
        //             if ($remaining_payment >= ($monthly_amount - $StudentTuitionFee->month_10_payment))
        //             {
        //                 $to_be_deduct = ($monthly_amount - $StudentTuitionFee->month_10_payment);
        //                 $StudentTuitionFee->month_10_payment += $to_be_deduct;
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $to_be_deduct;
        //                 $remaining_payment = $remaining_payment - $to_be_deduct;
        //             }
        //             else
        //             {
        //                 $StudentTuitionFee->month_10_payment += $remaining_payment; 
        //                 $StudentTuitionFee->total_remaining = $StudentTuitionFee->total_remaining - $remaining_payment;
        //                 $remaining_payment = 0;
        //             }
        //         }
        //     }
        // }

        // if ($StudentTuitionFee->total_remaining <= 0)
        // {
        //     $StudentTuitionFee->total_remaining = 0;
        //     $StudentTuitionFee->fully_paid = 1;
        // }

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
                                    },
                                    'discount_list',
                                    'grade_tuition',
                                    'additional_fee'
                                ])
                                ->where(function ($query) use ($request){
                                   $query->whereRaw("concat(first_name, ' ', middle_name , ' ', last_name) like '%". $request->pdf_search_filter ."%' ");

                                    if ($request->pdf_filter_grade)
                                    {
                                        $query->where('grade_id', $request->pdf_filter_grade);
                                    }

                                    if ($request->pdf_filter_section)
                                    {
                                        $query->where('section_id', $request->pdf_filter_section);
                                    }
                                })
                                ->where('status', 1)
                                ->orderBy('grade_id', 'ASC')
                                ->get();
        // $StudentTuitionFee = StudentTuitionFee::selectRaw('sum(total_remaining) as total_tuition_balance, sum(additional_fee) as total_additional_fee')
        //                             ->first();
        // return json_encode($Students);
        // PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        $pdf = PDF::loadView('cashier.student_payment.report.pdf_student_summary_balance', ['Students' => $Students]);
        return $pdf->stream();
        // return $pdf->download('Balance-Summary-List.pdf');

        return view('cashier.student_payment.report.pdf_student_summary_balance', ['Students' => $Students, 'StudentTuitionFee' => $StudentTuitionFee]);
    }
    
    public function student_summary_simple_balance (Request $request)
    {


        $Students = Student::with(['grade', 
                                    'section', 
                                    'tuition' => function ($query) {
                                        $query->where('status', 1);
                                    },
                                    'discount_list',
                                    'grade_tuition',
                                    'additional_fee'
                                ])
                                ->where(function ($query) use ($request){
                                   $query->whereRaw("concat(first_name, ' ', middle_name , ' ', last_name) like '%". $request->pdf_search_filter ."%' ");

                                    if ($request->pdf_filter_grade)
                                    {
                                        $query->where('grade_id', $request->pdf_filter_grade);
                                    }

                                    if ($request->pdf_filter_section)
                                    {
                                        $query->where('section_id', $request->pdf_filter_section);
                                    }
                                })
                                ->where('status', 1)
                                ->orderBy('grade_id', 'ASC')
                                ->get();

        $grade_selected = 'All';
        $section_selected = 'All';
        if ($request->pdf_filter_grade)
        {
            $Grade = Grade::where('id', $request->pdf_filter_grade)->first();
            if ($Grade)
            {
                $grade_selected = $Grade->grade;
            }
        }

        if ($request->pdf_filter_section)
        {
            $Section = Section::where('id', $request->pdf_filter_section)->first();
            if ($Section)
            {
                $section_selected = $Section->section_name;
            }
        }

        $pdf = PDF::loadView('cashier.student_payment.report.pdf_student_summary_simple_balance', ['Students' => $Students, 'grade_selected' => $grade_selected, 'section_selected' => $section_selected]);
        return $pdf->stream();
        return view('cashier.student_payment.report.pdf_student_summary_simple_balance', ['Students' => $Students, 'StudentTuitionFee' => $StudentTuitionFee, 'grade_selected' => $grade_selected, 'section_selected' => $section_selected]);
    }
    
}
