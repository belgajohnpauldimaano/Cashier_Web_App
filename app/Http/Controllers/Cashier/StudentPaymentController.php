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
                                ->where(function ($query) {
                                    // $query->where('grade_id', 1 );
                                    // $query->where('section_id', 1);
                                })
                                ->orderBy('grade_id', 'ASC')
                                ->paginate(10);
        $Student_tuition = Student::with(['grade_tuition' => function ($query) {
                                            $query->select(['grade_id', 'tuition_fee']);
                                        }
                                    ])
                                    ->where('grade_id', 1)
                                    ->where('section_id', 1)
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
                                // ->selectRaw('
                                //     ROUND(
                                //         (students.first_name IN("john", "paul")) * 100, 0) 
                                //     as email_rating
                                // ')
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
        $upon_enrollment = $Student->grade_tuition[0]->upon_enrollment;
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

        // $monthly = $misc_fee + ($net_tuition >= 2000 ? 2000 : $net_tuition);

        // if ($down_payment < $monthly)
        // // if ($down_payment == 0)
        // {
        //     $monthly = $monthly - $down_payment;
        //     if ($monthly > $net_tuition && $down_payment == 0)
        //     {
        //         $monthly = $net_tuition;
        //     }
        // }
        // else
        // {
        //     // $monthly = ($net_tuition - $down_payment) / 10;
        //     $monthly = ($total_tuition - $down_payment) / 10;
        // }
        
        // if ($monthly > $outstanding_balance)
        // {
        //     $monthly = $outstanding_balance;
        // }

        $monthly = ($total_tuition - $upon_enrollment) / 10;
        
        if ($down_payment < $upon_enrollment)
        {
            $monthly = $upon_enrollment - $down_payment;
        }
        else if ($monthly > $outstanding_balance)
        {
            $monthly = $outstanding_balance;
        }

        return view('cashier.student_payment.partials.form_modal_tuition_payment', ['student_id' => $Student->id, 'outstanding_balance' => $outstanding_balance, 'misc_fee' => $misc_fee, 'tuition' => $tuition , 'total_tuition' => $total_tuition, 'monthly' => $monthly, 'net_tuition_no_discount' => $net_tuition_no_discount, 'total_tuition_payment' => $total_tuition_payment, 'discount' => $discount, 'upon_enrollment' => $upon_enrollment])->render();
    }

    public function tuition_payment_process (Request $request)
    {
        if ($request->payment < 1)
        {
            return json_encode(['code' => 2, 'general_message' => 'Invalid payment.']);
        }
        
        $Validator = Validator::make($request->all(), [
                                        'payment' => 'required|digits_between:3,6',
                                        'or_number' => 'required|unique:student_payment_logs',
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
        $upon_enrollment = $Student->grade_tuition[0]->upon_enrollment;
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

        $monthly_amount = ($total_tuition - $upon_enrollment) / 10;
        
        if ($StudentTuitionFee->total_payment > $net_tuition)
        {
            return json_encode(['code' => 1, 'general_message' => 'Payment too large.']);
        }

        
        if ($down_payment < $upon_enrollment)
        {
            $monthly_amount = $upon_enrollment - $down_payment;
        }
        else if ($monthly_amount > $outstanding_balance)
        {
            $monthly = $outstanding_balance;
        }
        
        if ($down_payment < $upon_enrollment)
        {
            if ($monthly_amount > $net_tuition)
            {
                $monthly_amount = $net_tuition;
            }

            if ($StudentTuitionFee->total_payment > $net_tuition)
            {
                return json_encode(['code' => 1, 'general_message' => 'Payment too large.']);
            }
            

            if ($monthly_amount > $remaining_payment)
            {
                $monthly_amount = $remaining_payment;
            }
            
            $StudentTuitionFee->down_payment += $monthly_amount;
        }
        
        
        $monthly_amount = ($total_tuition - $upon_enrollment) / 10;
        $remaining_payment = $StudentTuitionFee->total_payment - $StudentTuitionFee->down_payment - $StudentTuitionFee->month_1_payment - $StudentTuitionFee->month_2_payment - $StudentTuitionFee->month_3_payment - $StudentTuitionFee->month_4_payment - $StudentTuitionFee->month_5_payment - $StudentTuitionFee->month_6_payment - $StudentTuitionFee->month_7_payment - $StudentTuitionFee->month_8_payment - $StudentTuitionFee->month_9_payment - $StudentTuitionFee->month_10_payment;

        
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
        
        //return json_encode(['StudentTuitionFee'=>$StudentTuitionFee, 'monthly_amount' => $monthly_amount, 'remaining_payment' => $remaining_payment]);
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
        $selected_grade = 'All';
        $selected_section = 'All';
        if ($request->pdf_filter_grade)
        {
            $selected_grade = Grade::where('id', $request->pdf_filter_grade)->first()->grade;
        }

        if ($request->pdf_filter_section)
        {
            $selected_section = Section::where('id', $request->pdf_filter_section)->first()->section_name;
        }
        $pdf = PDF::loadView('cashier.student_payment.report.pdf_student_summary_balance', ['Students' => $Students, 'selected_grade' => $selected_grade, 'selected_section' => $selected_section]);
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf ->get_canvas();
        $canvas->page_text(5, 5, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 7, array(0, 0, 0));
        return $pdf->stream();
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
        else
        {
            $Grade = Grade::all(['grade']);
            if ($Grade)
            {
                $grade_selected = $Grade;
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
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf ->get_canvas();
        $canvas->page_text(5, 5, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 7, array(0, 0, 0));
        return $pdf->stream();
    }
    
}
