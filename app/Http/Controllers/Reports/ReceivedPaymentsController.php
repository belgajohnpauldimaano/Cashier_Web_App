<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use PDF;

use App\Student;
use App\Grade;
use App\Section;
use App\StudentTuitionFee;
use App\StudentPaymentLog;
use App\AdditionalFeePayment;
use App\SchoolYear;
class ReceivedPaymentsController extends Controller
{
    public function index ()
    {
        $SchoolYear = SchoolYear::first(); 
        $sy_id = '';
        if ($SchoolYear)
        {
            $sy_id = $SchoolYear->id;
        }
        $StudentPaymentLog = StudentPaymentLog::with(['student', 'student.student_school_year_tag', 'user'])->where('school_year_id', $sy_id)->paginate(10);
        $payment_sum = StudentPaymentLog::selectRaw('SUM(payment) as sum')->where('school_year_id', $sy_id)->first();

        $Grade = Grade::all();
        $Section = Section::where('grade_id', 1)->get();
        $SchoolYear = SchoolYear::all();
        // return json_encode($StudentPaymentLog);
        return view('reports.received_payments.index', ['StudentPaymentLog' => $StudentPaymentLog, 'Grade' => $Grade, 'Section' => $Section, 'payment_sum' => $payment_sum, 'SchoolYear' => $SchoolYear]);
    }

    public function list (Request $request)
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
        $StudentPaymentLog = StudentPaymentLog::with(['student','student.student_school_year_tag', 'student.grade', 'student.section', 'user'])
                                        ->where(function ($query) use ($request){
                                            
                                            if ($request->filter_start_date)
                                            {
                                                $query->where('received_date', '>=', date('Y-m-d H:i:s', strtotime($request->filter_start_date . ' 00:00:00')));
                                            }

                                            if ($request->filter_end_date)
                                            {
                                                $query->where('received_date', '<=', date('Y-m-d H:i:s', strtotime($request->filter_end_date . ' 23:59:00')));
                                            }

                                            if ($request->payment_type)
                                            {
                                                if ($request->payment_type == 1)
                                                {
                                                    $query->where('payment_type', 1);
                                                }
                                                else if ($request->payment_type == 6)
                                                {
                                                    $query->where('payment_type', '>', 1);
                                                }
                                                else if ($request->payment_type > 1)
                                                {
                                                    $query->where('payment_type', $request->payment_type);
                                                }
                                            }
                                        })
                                        ->where('school_year_id', $request->filter_school_year)
                                        ->whereHas('student', function ($query) use ($request){
                                             $query->whereRaw("concat(first_name, ' ', middle_name , ' ', last_name) like '%". $request->search_filter ."%' ");
                                        
                                            // if ($request->filter_grade)
                                            // {
                                            //     $query->where('grade_id', $request->filter_grade);
                                            // }

                                            // if ($request->filter_section)
                                            // {
                                            //     $query->where('section_id', $request->filter_section);
                                            // }
                                        })
                                        ->whereHas('student.student_school_year_tag', function ($query) use ($request){                                        
                                            if ($request->filter_grade)
                                            {
                                                $query->where('grade_id', $request->filter_grade);
                                            }

                                            if ($request->filter_section)
                                            {
                                                $query->where('section_id', $request->filter_section);
                                            }
                                        })
                                        ->orWhere(function ($query) use ($request){
                                            if ($request->search_filter)
                                            {
                                                $query->where('or_number', 'like', '%'. $request->search_filter .'%');
                                            }
                                        })
                                        ->paginate($pages);
        $payment_sum = StudentPaymentLog::where(function ($query) use ($request){
                                            
                                            if ($request->filter_start_date)
                                            {
                                                $query->where('received_date', '>=', date('Y-m-d H:i:s', strtotime($request->filter_start_date . ' 00:00:00')));
                                            }

                                            if ($request->filter_end_date)
                                            {
                                                $query->where('received_date', '<=', date('Y-m-d H:i:s', strtotime($request->filter_end_date . ' 23:59:00')));
                                            }

                                            if ($request->payment_type)
                                            {
                                                if ($request->payment_type == 1)
                                                {
                                                    $query->where('payment_type', 1);
                                                }
                                                else if ($request->payment_type == 6)
                                                {
                                                    $query->where('payment_type', '>', 1);
                                                }
                                                else if ($request->payment_type > 1)
                                                {
                                                    $query->where('payment_type', $request->payment_type);
                                                }
                                            }
                                        })
                                        ->where('school_year_id', $request->filter_school_year)
                                        ->whereHas('student', function ($query) use ($request){
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
                                        ->orWhere(function ($query) use ($request){
                                            if ($request->search_filter)
                                            {
                                                $query->where('or_number', 'like', '%'. $request->search_filter .'%');
                                            }
                                        })
                                        ->selectRaw('SUM(payment) as sum')->first();
                                        // return json_encode($StudentPaymentLog);
        return view('reports.received_payments.partials.list', ['StudentPaymentLog' => $StudentPaymentLog, 'payment_sum' => $payment_sum, 'request' => $request->all()])->render();
    }

    public function export_pdf_received_payments (Request $request)
    {

        $StudentPaymentLog = StudentPaymentLog::with(['student','student.student_school_year_tag', 'student.grade', 'student.section', 'user'])
                                        ->where(function ($query) use ($request){
                                            
                                            if ($request->filter_start_date)
                                            {
                                                $query->where('received_date', '>=', date('Y-m-d H:i:s', strtotime($request->filter_start_date . ' 00:00:00')));
                                            }

                                            if ($request->filter_end_date)
                                            {
                                                $query->where('received_date', '<=', date('Y-m-d H:i:s', strtotime($request->filter_end_date . ' 23:59:00')));
                                            }

                                            if ($request->report_payment_type)
                                            {
                                                if ($request->report_payment_type == 1)
                                                {
                                                    $query->where('payment_type', 1);
                                                }
                                                else if ($request->report_payment_type == 6)
                                                {
                                                    $query->where('payment_type', '>', 1);
                                                }
                                                else if ($request->report_payment_type > 1)
                                                {
                                                    $query->where('payment_type', $request->report_payment_type);
                                                }
                                            }
                                        })
                                        ->where('school_year_id', $request->report_school_year)
                                        ->whereHas('student', function ($query) use ($request){
                                             $query->whereRaw("concat(first_name, ' ', middle_name , ' ', last_name) like '%". $request->report_search_filter ."%' ");
                                        
                                            // if ($request->report_filter_grade)
                                            // {
                                            //     $query->where('grade_id', $request->report_filter_grade);
                                            // }

                                            // if ($request->report_filter_section)
                                            // {
                                            //     $query->where('section_id', $request->report_filter_section);
                                            // }
                                        })
                                        ->whereHas('student.student_school_year_tag', function ($query) use ($request){
                                            
                                           if ($request->report_filter_grade)
                                           {
                                               $query->where('grade_id', $request->report_filter_grade);
                                           }

                                           if ($request->report_filter_section)
                                           {
                                               $query->where('section_id', $request->report_filter_section);
                                           }
                                       })
                                        ->orWhere(function ($query) use ($request){
                                            if ($request->search_filter)
                                            {
                                                $query->where('or_number', 'like', '%'. $request->search_filter .'%');
                                            }
                                        })
                                        ->get();

        $payment_sum = StudentPaymentLog::where(function ($query) use ($request){
                                            
                                            if ($request->filter_start_date)
                                            {
                                                $query->where('received_date', '>=', date('Y-m-d H:i:s', strtotime($request->filter_start_date . ' 00:00:00')));
                                            }

                                            if ($request->filter_end_date)
                                            {
                                                $query->where('received_date', '<=', date('Y-m-d H:i:s', strtotime($request->filter_end_date . ' 23:59:00')));
                                            }

                                            if ($request->report_payment_type)
                                            {
                                                if ($request->report_payment_type == 1)
                                                {
                                                    $query->where('payment_type', 1);
                                                }
                                                else if ($request->report_payment_type == 6)
                                                {
                                                    $query->where('payment_type', '>', 1);
                                                }
                                                else if ($request->report_payment_type > 1)
                                                {
                                                    $query->where('payment_type', $request->report_payment_type);
                                                }
                                            }
                                        })
                                        ->where('school_year_id', $request->report_school_year)
                                        ->whereHas('student', function ($query) use ($request){
                                             $query->whereRaw("concat(first_name, ' ', middle_name , ' ', last_name) like '%". $request->report_search_filter ."%' ");
                                        
                                            if ($request->report_filter_grade)
                                            {
                                                $query->where('grade_id', $request->report_filter_grade);
                                            }

                                            if ($request->report_filter_section)
                                            {
                                                $query->where('section_id', $request->report_filter_section);
                                            }
                                        })
                                        ->orWhere(function ($query) use ($request){
                                            if ($request->search_filter)
                                            {
                                                $query->where('or_number', 'like', '%'. $request->search_filter .'%');
                                            }
                                        })
                                        ->selectRaw('SUM(payment) as sum')->first();

        $range_from = $request->filter_start_date;
        $range_to = $request->filter_end_date;
        // return json_encode($Students);
        // PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        $pdf = PDF::loadView('reports.received_payments.report.pdf_receivedpayments', ['StudentPaymentLog' => $StudentPaymentLog, 'payment_sum' => $payment_sum, 'range_from' => $range_from, 'range_to' => $range_to])->setPaper('letter', 'landscape');
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf ->get_canvas();
        $canvas->page_text(5, 5, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 7, array(0, 0, 0));
        return $pdf->stream();
        // return $pdf->download('Balance-Summary-List.pdf'

        return view('reports.received_payments.report.pdf_receivedpayments', ['StudentPaymentLog' => $StudentPaymentLog, 'payment_sum' => $payment_sum, 'range_from' => $range_from, 'range_to' => $range_to]);
    }

    public function received_payments_summary_report (Request $request)
    {
        $grade_payments = [];

        $Grade = Grade::where(function ($query) use ($request) {
                            if ($request->report_filter_grade)
                            {
                                $query->where('id', $request->report_filter_grade);
                            }

                        })
                        ->get(['id', 'grade']);
                        
        foreach ($Grade as $grd)
        {
            $StudentPaymentLog = StudentPaymentLog::join('student_school_year_tags', 'student_school_year_tags.student_id', '=', 'student_payment_logs.student_id')
                                                    ->where(function ($query) use ($grd) {
                                                        $query->whereRaw('student_school_year_tags.grade_id = ' . $grd->id);
                                                    })
                                                    ->where(function ($query) use ($request) {
                                                        if ($request->filter_start_date)
                                                        {
                                                            $query->whereRaw('student_payment_logs.received_date >= "' . date('Y-m-d H:i:s', strtotime($request->filter_start_date . ' 00:00:00')) . '"');
                                                        }

                                                        if ($request->filter_end_date)
                                                        {
                                                            $query->whereRaw('student_payment_logs.received_date <= "' . date('Y-m-d H:i:s', strtotime($request->filter_end_date . ' 23:59:00')) . '"');
                                                        }
                                                    })
                                                    ->where(function ($query) use ($request) {
                                                        if ($request->report_payment_type)
                                                        {
                                                            if ($request->report_payment_type == 1)
                                                            {
                                                                $query->where('payment_type', 1);
                                                            }
                                                            else if ($request->report_payment_type == 6)
                                                            {
                                                                $query->where('payment_type', '>', 1);
                                                            }
                                                            else if ($request->report_payment_type > 1)
                                                            {
                                                                $query->where('payment_type', $request->report_payment_type);
                                                            }
                                                        }
                                                    })
                                                    ->where('student_school_year_tags.school_year_id', $request->report_school_year)
                                                    ->where('student_payment_logs.school_year_id', $request->report_school_year)
                                                    ->selectRaw('
                                                        SUM(payment) as total_payment, student_school_year_tags.grade_id
                                                    ')
                                                    ->first();
            if ($StudentPaymentLog->total_payment != NULL)
            {
                $grade_payments[] = $StudentPaymentLog;
            }
        }

        $range_from             = $request->filter_start_date;
        $range_to               = $request->filter_end_date;
        $report_payment_type    = ($request->report_payment_type ? $request->report_payment_type : 0);
        
        $payment_type  = [  
            'All types of payment',
            'Tuition Fees',
            'Books',
            'Speech Lab',
            'P.E Uniform/Set',
            'School Uniform/Set',
            'Other Fees',
        ];

        $pdf = PDF::loadView('reports.received_payments.report.pdf_receivedpayments_summary', ['grade_payments' => $grade_payments, 'Grade' => $Grade, 'range_from' => $range_from, 'range_to' => $range_to, 'report_payment_type' => $report_payment_type, 'payment_type' => $payment_type])->setPaper('letter', 'landscape');
        
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf ->get_canvas();
        $canvas->page_text(5, 5, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 7, array(0, 0, 0));
        return $pdf->stream();

        return view('reports.received_payments.report.pdf_receivedpayments_summary', ['grade_payments' => $grade_payments, 'Grade' => $Grade]);
    }

    public function edit_payment_modal (Request $request) 
    {
        $StudentPaymentLog = StudentPaymentLog::with(['student', 'student.grade', 'student.section', 'user', 'student.grade.additional_fee'])
        ->where('id', $request->id)
        ->where('school_year_id', $request->sy_id)
        ->first();
        
        $payment_type  = [  
            'Books',
            'Speech Lab',
            'P.E Uniform/Set',
            'School Uniform/Set'
        ];
        return view('reports.received_payments.partials.form_modal', ['StudentPaymentLog' => $StudentPaymentLog, 'payment_type' => $payment_type])->render();
        return json_encode($StudentPaymentLog);
    }

    public function save_edit_entry (Request $request) 
    {
        $rules = [
            'payment_type' => 'required',
            'amount' => 'required|numeric'
        ];
        $messages = [
            'payment_type.required' => 'Payment type is required',
            'amount.required' => 'Amount is required'
        ];
        $Validator = \Validator::make($request->all(), $rules, $messages);
        
        if ($Validator->fails())
        {
            return json_encode(['code' => 1 ,'general_message' => 'Please fill all required fields.', 'messages' => $Validator->getMessageBag()]);
        }
        
        $StudentPaymentLog = StudentPaymentLog::with(['student', 'student.grade', 'student.section', 'user', 'student.grade.additional_fee'])
        ->where('id', $request->id)
        ->where('school_year_id', $request->sy_id)
        ->first();

        if (!$StudentPaymentLog)
        {
            return json_encode(['code' => 2 ,'general_message' => 'Invalid selection of data.']);  
        }

        if ($StudentPaymentLog->payment_type != 1)
        {
            if ($StudentPaymentLog->student)
            {
                if ($StudentPaymentLog->student->grade)
                {
                    if ($StudentPaymentLog->student->grade->additional_fee)
                    {
                        foreach ($StudentPaymentLog->student->grade->additional_fee as $key => $data) 
                        {
                            if ($key + 2 == $request->payment_type)
                            {
                                if ($request->amount > $data->additional_amount)
                                {
                                    return json_encode(['code' => 2 ,'general_message' => 'Your payment is too large for the balance.']);
                                }
                            }
                        }
                    }   
                }   
            }

            $StudentPaymentLog = StudentPaymentLog::where('id', $request->id)
            ->where('school_year_id', $request->sy_id)
            ->first();

            $StudentPaymentLog->payment_type = $request->payment_type;
            $StudentPaymentLog->payment = $request->amount;
            $StudentPaymentLog->save();

            $AdditionalFeePayment = AdditionalFeePayment::where('student_id', $StudentPaymentLog->student_id)
            ->where('school_year_id', $request->sy_id)
            ->first();

            if ($request->payment_type == 2)
            {
                $AdditionalFeePayment->books = $request->amount;
                $AdditionalFeePayment->speech_lab = 0;
                $AdditionalFeePayment->pe_uniform = 0;
                $AdditionalFeePayment->school_uniform = 0;
            }
            else if ($request->payment_type == 3)
            {
                $AdditionalFeePayment->books = 0;
                $AdditionalFeePayment->speech_lab = $request->amount;
                $AdditionalFeePayment->pe_uniform = 0;
                $AdditionalFeePayment->school_uniform = 0;
            }
            else if ($request->payment_type == 4)
            {
                $AdditionalFeePayment->books = 0;
                $AdditionalFeePayment->speech_lab = 0;
                $AdditionalFeePayment->pe_uniform = $request->amount;
                $AdditionalFeePayment->school_uniform = 0;
            }
            else if ($request->payment_type == 5)
            {
                $AdditionalFeePayment->books = 0;
                $AdditionalFeePayment->speech_lab = 0;
                $AdditionalFeePayment->pe_uniform = 0;
                $AdditionalFeePayment->school_uniform = $request->amount;
            }
            $AdditionalFeePayment->save();

            return json_encode(['code' => 0 ,'general_message' => 'Payment entry successfully updated.']);
        }
        else
        {
            $StudentTuitionFee = StudentTuitionFee::where('student_id', $StudentPaymentLog->student_id)->where('school_year_id', $request->sy_id)->first();
            $StudentTuitionFee->month_1_payment = 0;
            $StudentTuitionFee->month_2_payment = 0;
            $StudentTuitionFee->month_3_payment = 0;
            $StudentTuitionFee->month_4_payment = 0;
            $StudentTuitionFee->month_5_payment = 0;
            $StudentTuitionFee->month_6_payment = 0;
            $StudentTuitionFee->month_7_payment = 0;
            $StudentTuitionFee->month_8_payment = 0;
            $StudentTuitionFee->month_9_payment = 0;
            $StudentTuitionFee->month_10_payment = 0;
            $old_total_payment = $StudentTuitionFee->total_payment;

            $total_tuition_payment = $StudentTuitionFee->total_payment - $StudentPaymentLog->payment + $request->amount;
            $down_payment = $StudentTuitionFee->down_payment;

            $StudentTuitionFee->total_payment = $total_tuition_payment;
    

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
            ->where('id', $StudentPaymentLog->student_id)
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

            $net_tuition = $total_tuition - $discount;
            $outstanding_balance = $net_tuition - $total_tuition_payment;
            $monthly_amount = ($total_tuition - $upon_enrollment) / 10;
            
            $StudentTuitionFee->total_remaining = $outstanding_balance;

            if ($total_tuition_payment > 0)
            {
                if ($total_tuition_payment > $upon_enrollment)
                {
                    $StudentTuitionFee->down_payment = $upon_enrollment;
                    $total_tuition_payment -= $upon_enrollment;
                }
                else
                {
                    $StudentTuitionFee->down_payment = $total_tuition_payment;
                    $total_tuition_payment = 0;
                }
            }

            
            if ($total_tuition_payment > 0)
            {
                if ($total_tuition_payment > $monthly_amount)
                {
                    $StudentTuitionFee->month_1_payment = $monthly_amount;
                    $total_tuition_payment = $total_tuition_payment - $monthly_amount;
                }
                else
                {
                    $StudentTuitionFee->month_1_payment = $total_tuition_payment;
                    $total_tuition_payment = 0;
                }
            }
            
            if ($total_tuition_payment > 0)
            {
                if ($total_tuition_payment > $monthly_amount)
                {
                    $StudentTuitionFee->month_2_payment = $monthly_amount;
                    $total_tuition_payment = $total_tuition_payment - $monthly_amount;
                }
                else
                {
                    $StudentTuitionFee->month_2_payment = $total_tuition_payment;
                    $total_tuition_payment = 0;
                }
            }
            
            if ($total_tuition_payment > 0)
            {
                if ($total_tuition_payment > $monthly_amount)
                {
                    $StudentTuitionFee->month_3_payment = $monthly_amount;
                    $total_tuition_payment = $total_tuition_payment - $monthly_amount;
                }
                else
                {
                    $StudentTuitionFee->month_3_payment = $total_tuition_payment;
                    $total_tuition_payment = 0;
                }
            }
            
            if ($total_tuition_payment > 0)
            {
                if ($total_tuition_payment > $monthly_amount)
                {
                    $StudentTuitionFee->month_4_payment = $monthly_amount;
                    $total_tuition_payment = $total_tuition_payment - $monthly_amount;
                }
                else
                {
                    $StudentTuitionFee->month_4_payment = $total_tuition_payment;
                    $total_tuition_payment = 0;
                }
            }
            
            if ($total_tuition_payment > 0)
            {
                if ($total_tuition_payment > $monthly_amount)
                {
                    $StudentTuitionFee->month_5_payment = $monthly_amount;
                    $total_tuition_payment = $total_tuition_payment - $monthly_amount;
                }
                else
                {
                    $StudentTuitionFee->month_5_payment = $total_tuition_payment;
                    $total_tuition_payment = 0;
                }
            }
            
            if ($total_tuition_payment > 0)
            {
                if ($total_tuition_payment > $monthly_amount)
                {
                    $StudentTuitionFee->month_6_payment = $monthly_amount;
                    $total_tuition_payment = $total_tuition_payment - $monthly_amount;
                }
                else
                {
                    $StudentTuitionFee->month_6_payment = $total_tuition_payment;
                    $total_tuition_payment = 0;
                }
            }
            
            if ($total_tuition_payment > 0)
            {
                if ($total_tuition_payment > $monthly_amount)
                {
                    $StudentTuitionFee->month_7_payment = $monthly_amount;
                    $total_tuition_payment = $total_tuition_payment - $monthly_amount;
                }
                else
                {
                    $StudentTuitionFee->month_7_payment = $total_tuition_payment;
                    $total_tuition_payment = 0;
                }
            }
            
            if ($total_tuition_payment > 0)
            {
                if ($total_tuition_payment > $monthly_amount)
                {
                    $StudentTuitionFee->month_8_payment = $monthly_amount;
                    $total_tuition_payment = $total_tuition_payment - $monthly_amount;
                }
                else
                {
                    $StudentTuitionFee->month_8_payment = $total_tuition_payment;
                    $total_tuition_payment = 0;
                }
            }
            
            if ($total_tuition_payment > 0)
            {
                if ($total_tuition_payment > $monthly_amount)
                {
                    $StudentTuitionFee->month_9_payment = $monthly_amount;
                    $total_tuition_payment = $total_tuition_payment - $monthly_amount;
                }
                else
                {
                    $StudentTuitionFee->month_9_payment = $total_tuition_payment;
                    $total_tuition_payment = 0;
                }
            }
            
            if ($total_tuition_payment > 0)
            {
                if ($total_tuition_payment > $monthly_amount)
                {
                    $StudentTuitionFee->month_10_payment = $monthly_amount;
                    $total_tuition_payment = $total_tuition_payment - $monthly_amount;
                }
                else
                {
                    $StudentTuitionFee->month_10_payment = $total_tuition_payment;
                    $total_tuition_payment = 0;
                }
            }

            $StudentTuitionFee->save();
            
            $StudentPaymentLog = StudentPaymentLog::where('id', $request->id)
            ->where('school_year_id', $request->sy_id)
            ->first();
            $StudentPaymentLog->payment = $request->amount;
            $StudentPaymentLog->or_number = $request->or_number;
            $StudentPaymentLog->save();

            return json_encode(['code' => 0 ,'general_message' => 'Tuition fee entry successfully updated.']);
            
            // return json_encode(['code' => 2 ,'general_message' => 'asas.', 'StudentTuitionFee' => $StudentTuitionFee, 
            // $old_total_payment, 
            // 'total_tuition_payment' => $total_tuition_payment, 
            // $total_tuition,
            // $discount,
            // $upon_enrollment,
            // 'net_tuition' => $net_tuition,
            // 'outstanding_balance' => $outstanding_balance,
            // 'monthly_amount' => $monthly_amount,
            // 'upon_enrollment' => $upon_enrollment]);
        }

        // return json_encode(['code' => 2 ,'general_message' => 'asas.']);
    }

    public function delete_entry (Request $request) 
    {
        $StudentPaymentLog = StudentPaymentLog::with(['student', 'student.grade', 'student.section', 'user', 'student.grade.additional_fee'])
        ->where('id', $request->id)
        ->first();

        if (!$StudentPaymentLog)
        {
            return json_encode(['code' => 2 ,'general_message' => 'Invalid selection of data']);
        }
        $StudentTuitionFee = StudentTuitionFee::where('student_id', $StudentPaymentLog->student_id)->first();
        $StudentTuitionFee->month_1_payment = 0;
        $StudentTuitionFee->month_2_payment = 0;
        $StudentTuitionFee->month_3_payment = 0;
        $StudentTuitionFee->month_4_payment = 0;
        $StudentTuitionFee->month_5_payment = 0;
        $StudentTuitionFee->month_6_payment = 0;
        $StudentTuitionFee->month_7_payment = 0;
        $StudentTuitionFee->month_8_payment = 0;
        $StudentTuitionFee->month_9_payment = 0;
        $StudentTuitionFee->month_10_payment = 0;
        $old_total_payment = $StudentTuitionFee->total_payment;

        $total_tuition_payment = $StudentTuitionFee->total_payment - $StudentPaymentLog->payment;
        $down_payment = $StudentTuitionFee->down_payment;

        $StudentTuitionFee->total_payment = $total_tuition_payment;


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
        ->where('id', $StudentPaymentLog->student_id)
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

        $net_tuition = $total_tuition - $discount;
        $outstanding_balance = $net_tuition - $total_tuition_payment;
        $monthly_amount = ($total_tuition - $upon_enrollment) / 10;
        
        $StudentTuitionFee->total_remaining = $outstanding_balance;

        if ($total_tuition_payment > 0)
        {
            if ($total_tuition_payment > $upon_enrollment)
            {
                $StudentTuitionFee->down_payment = $upon_enrollment;
                $total_tuition_payment -= $upon_enrollment;
            }
            else
            {
                $StudentTuitionFee->down_payment = $total_tuition_payment;
                $total_tuition_payment = 0;
            }
        }

        
        if ($total_tuition_payment > 0)
        {
            if ($total_tuition_payment > $monthly_amount)
            {
                $StudentTuitionFee->month_1_payment = $monthly_amount;
                $total_tuition_payment = $total_tuition_payment - $monthly_amount;
            }
            else
            {
                $StudentTuitionFee->month_1_payment = $total_tuition_payment;
                $total_tuition_payment = 0;
            }
        }
        
        if ($total_tuition_payment > 0)
        {
            if ($total_tuition_payment > $monthly_amount)
            {
                $StudentTuitionFee->month_2_payment = $monthly_amount;
                $total_tuition_payment = $total_tuition_payment - $monthly_amount;
            }
            else
            {
                $StudentTuitionFee->month_2_payment = $total_tuition_payment;
                $total_tuition_payment = 0;
            }
        }
        
        if ($total_tuition_payment > 0)
        {
            if ($total_tuition_payment > $monthly_amount)
            {
                $StudentTuitionFee->month_3_payment = $monthly_amount;
                $total_tuition_payment = $total_tuition_payment - $monthly_amount;
            }
            else
            {
                $StudentTuitionFee->month_3_payment = $total_tuition_payment;
                $total_tuition_payment = 0;
            }
        }
        
        if ($total_tuition_payment > 0)
        {
            if ($total_tuition_payment > $monthly_amount)
            {
                $StudentTuitionFee->month_4_payment = $monthly_amount;
                $total_tuition_payment = $total_tuition_payment - $monthly_amount;
            }
            else
            {
                $StudentTuitionFee->month_4_payment = $total_tuition_payment;
                $total_tuition_payment = 0;
            }
        }
        
        if ($total_tuition_payment > 0)
        {
            if ($total_tuition_payment > $monthly_amount)
            {
                $StudentTuitionFee->month_5_payment = $monthly_amount;
                $total_tuition_payment = $total_tuition_payment - $monthly_amount;
            }
            else
            {
                $StudentTuitionFee->month_5_payment = $total_tuition_payment;
                $total_tuition_payment = 0;
            }
        }
        
        if ($total_tuition_payment > 0)
        {
            if ($total_tuition_payment > $monthly_amount)
            {
                $StudentTuitionFee->month_6_payment = $monthly_amount;
                $total_tuition_payment = $total_tuition_payment - $monthly_amount;
            }
            else
            {
                $StudentTuitionFee->month_6_payment = $total_tuition_payment;
                $total_tuition_payment = 0;
            }
        }
        
        if ($total_tuition_payment > 0)
        {
            if ($total_tuition_payment > $monthly_amount)
            {
                $StudentTuitionFee->month_7_payment = $monthly_amount;
                $total_tuition_payment = $total_tuition_payment - $monthly_amount;
            }
            else
            {
                $StudentTuitionFee->month_7_payment = $total_tuition_payment;
                $total_tuition_payment = 0;
            }
        }
        
        if ($total_tuition_payment > 0)
        {
            if ($total_tuition_payment > $monthly_amount)
            {
                $StudentTuitionFee->month_8_payment = $monthly_amount;
                $total_tuition_payment = $total_tuition_payment - $monthly_amount;
            }
            else
            {
                $StudentTuitionFee->month_8_payment = $total_tuition_payment;
                $total_tuition_payment = 0;
            }
        }
        
        if ($total_tuition_payment > 0)
        {
            if ($total_tuition_payment > $monthly_amount)
            {
                $StudentTuitionFee->month_9_payment = $monthly_amount;
                $total_tuition_payment = $total_tuition_payment - $monthly_amount;
            }
            else
            {
                $StudentTuitionFee->month_9_payment = $total_tuition_payment;
                $total_tuition_payment = 0;
            }
        }
        
        if ($total_tuition_payment > 0)
        {
            if ($total_tuition_payment > $monthly_amount)
            {
                $StudentTuitionFee->month_10_payment = $monthly_amount;
                $total_tuition_payment = $total_tuition_payment - $monthly_amount;
            }
            else
            {
                $StudentTuitionFee->month_10_payment = $total_tuition_payment;
                $total_tuition_payment = 0;
            }
        }

        $StudentTuitionFee->save();
        
        $StudentPaymentLog = StudentPaymentLog::where('id', $request->id)->first();
        $StudentPaymentLog->delete();

        return json_encode(['code' => 0 ,'general_message' => 'Payment entry has been deleted.']);
        return json_encode([$StudentPaymentLog, $StudentTuitionFee]);
    }
}
