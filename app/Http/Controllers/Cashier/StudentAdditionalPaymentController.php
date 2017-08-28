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
use App\AdditionalFee;
use App\AdditionalFeePayment;

class StudentAdditionalPaymentController extends Controller
{
    public function index ()
    {
        $Students = Student::with(['grade', 
                                    'section', 
                                    'tuition' => function ($query) {
                                        $query->where('status', 1);
                                    },
                                    'additional_fee',
                                    'additional_fee_payment'
                                ])
                                ->where('status', 1)
                                ->orderBy('grade_id', 'ASC')
                                ->orderBy('last_name', 'ASC')
                                ->paginate(10);
                                
        $Grade      = Grade::all();
        $Section    = Section::where('grade_id', 1)->get();

        // return json_encode($Students);
        return view('cashier.student_additional_payment.index', ['Students' => $Students, 'Grade' => $Grade, 'Section' => $Section]);
    }

    public function list_data (Request $request) 
    {
        $pages = 10;
        if ($request->show_count)
        {
            $pages = $request->show_count;
        }

        $Students = Student::with(['grade', 
                                'section', 
                                'tuition' => function ($query) {
                                    $query->where('status', 1);
                                },
                                'additional_fee',
                                'additional_fee_payment'
                            ])
                            ->where('status', 1)
                            ->where(function ($query) use ($request) {
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
                            ->orderBy('grade_id', 'ASC')
                            ->paginate($pages);
            
        $Grade      = Grade::all();
        $Section    = Section::where('grade_id', 1)->get();

        // return json_encode($Students);
        return view('cashier.student_additional_payment.partials.list_data', ['Students' => $Students, 'request' => $request->all()])->render();
    }

    public function form_modal_additional_payment (Request $request)
    {
        if (!$request->id)
        {

        }

        $AdditionalFeePayment = AdditionalFeePayment::where('student_id', $request->id)->first();

        if ($AdditionalFeePayment == NULL)
        {
            $Student = Student::where('id', $request->id)->first();
            if ($Student != NULL)
            {
                $AdditionalFeePayment = new \App\AdditionalFeePayment();
                $AdditionalFeePayment->student_id = $request->id;
                $AdditionalFeePayment->save();
            }
        }

        $Student = Student::with(['grade', 
                                    'section', 
                                    'tuition' => function ($query) {
                                        $query->where('status', 1);
                                    },
                                    'additional_fee',
                                    'additional_fee_payment'
                                ])
                                ->where('status', 1)
                                ->where('id', $request->id)
                                ->first();
        $total_additional_fee = 0 ;
        $total_additional_payment = 0;
        $outstanding_balance = 0;
        $individual_fee = [];
        $individual_payment = [];
        if ($Student->additional_fee)
        {
            foreach ($Student->additional_fee as $additional_fee)
            {
                $total_additional_fee += $additional_fee->additional_amount;
                $individual_fee[] = $additional_fee->additional_amount;
            }
        }
        
        if ($Student->additional_fee_payment)
        {
            $total_additional_payment += $Student->additional_fee_payment->books;
            $total_additional_payment += $Student->additional_fee_payment->speech_lab;
            $total_additional_payment += $Student->additional_fee_payment->pe_uniform;
            $total_additional_payment += $Student->additional_fee_payment->school_uniform;

            $individual_payment[] = $Student->additional_fee_payment->books;
            $individual_payment[] = $Student->additional_fee_payment->speech_lab;
            $individual_payment[] = $Student->additional_fee_payment->pe_uniform;
            $individual_payment[] = $Student->additional_fee_payment->school_uniform;
        }
        
        $outstanding_balance = $total_additional_fee - $total_additional_payment;

        return view('cashier.student_additional_payment.partials.form_modal_additional_payment', ['Student' => $Student, 'outstanding_balance' => $outstanding_balance, 'individual_fee' => $individual_fee, 'individual_payment' => $individual_payment, 'total_additional_payment' => $total_additional_payment, 'total_additional_fee' => $total_additional_fee])->render();
    }

    public function process_payment (Request $request)
    {
        $rules = [
            'id'            => 'required',
            'payment'       => 'required',
            'or_number'     => 'required|unique:student_payment_logs',
            'date_received' => 'required|date_format:Y-m-d',
            'fee_type'       => 'required',
        ];

        $messages = [
            'id.required'               => 'Invalid selection.',
            'payment.required'          => 'Payment is required.',
            'or_number.required'        => 'OR number is required.',
            'or_number.unique'          => 'OR number already used.',
            'date_received.required'    => 'Date received is required.',
            'date_received.date_format' => 'Date received should be a valid date format(yyyy-mm-dd)',
            'fee_type'                  => 'Fee type is required.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails())
        {
            return response()->json(['code' => 1, 'general_message' => 'Please fill all fields.', 'messages' => $validator->getMessageBag()], 200);    
        }
        
        $Student = Student::with(['additional_fee'])->where('id', $request->id)->first();

        if (!$Student)
        {
            return response()->json(['code' => 1, 'general_message' => 'Invalid selection of record.', 'messages' => []], 200);    
        }

        $AdditionalFeePayment = AdditionalFeePayment::where('student_id', $Student->id)->first();
        $fee_amount = '';
        if ($request->fee_type == 0)
        {
            $fee_amount = $Student->additional_fee->where('additional_title','Books (Annually)')->first()->additional_amount;
            if ($fee_amount <= $AdditionalFeePayment->books)
            {
                return response()->json(['code' => 1, 'general_message' => 'Already paid for books.', 'messages' => []], 200);       
            }
            
            if ($AdditionalFeePayment->books + $request->payment > $fee_amount)
            {  
                return response()->json(['code' => 1, 'general_message' => 'Payment is too large for the amount to be paid.', 'messages' => []]); 
            }

            $AdditionalFeePayment->books += $request->payment;
        }
        else if ($request->fee_type == 1)
        {
            $fee_amount = $Student->additional_fee->where('additional_title','Speech Lab (Annually)')->first()->additional_amount;
            if ($fee_amount <= $AdditionalFeePayment->speech_lab)
            {
                return response()->json(['code' => 1, 'general_message' => 'Already paid for books.', 'messages' => []], 200);       
            }
            
            if ($AdditionalFeePayment->speech_lab + $request->payment > $fee_amount)
            {  
                return response()->json(['code' => 1, 'general_message' => 'Payment is too large for the amount to be paid.', 'messages' => []]); 
            }

            $AdditionalFeePayment->speech_lab += $request->payment;
        }
        else if ($request->fee_type == 2)
        {
            $fee_amount = $Student->additional_fee->where('additional_title','P.E Uniform/Set')->first()->additional_amount;
            if ($fee_amount <= $AdditionalFeePayment->pe_uniform)
            {
                return response()->json(['code' => 1, 'general_message' => 'Already paid for books.', 'messages' => []], 200);       
            }
            
            if ($AdditionalFeePayment->pe_uniform + $request->payment > $fee_amount)
            {  
                return response()->json(['code' => 1, 'general_message' => 'Payment is too large for the amount to be paid.', 'messages' => []]); 
            }

            $AdditionalFeePayment->pe_uniform += $request->payment;
        }
        else if ($request->fee_type == 3)
        {
            $fee_amount = $Student->additional_fee->where('additional_title','School Uniform/Set')->first()->additional_amount;
            if ($fee_amount <= $AdditionalFeePayment->school_uniform)
            {
                return response()->json(['code' => 1, 'general_message' => 'Already paid for books.', 'messages' => []], 200);       
            }
            
            if ($AdditionalFeePayment->school_uniform + $request->payment > $fee_amount)
            {  
                return response()->json(['code' => 1, 'general_message' => 'Payment is too large for the amount to be paid.', 'messages' => []]); 
            }

            $AdditionalFeePayment->school_uniform += $request->payment;
        }


        $AdditionalFeePayment->save();
        
        $StudentPaymentLog = new StudentPaymentLog();
        $StudentPaymentLog->student_id  = $request->id;
        $StudentPaymentLog->payment     = $request->payment;
        $StudentPaymentLog->payment_type= $request->fee_type + 2;
        $StudentPaymentLog->received_date= \Carbon\Carbon::parse($request->date_received)->format('Y-m-d H:i:s');
        $StudentPaymentLog->or_number   = $request->or_number;
        $StudentPaymentLog->received_by = Auth::user()->id;
        $StudentPaymentLog->save();

        return response()->json(['code' => 0, 'general_message' => 'Payment success'], 200);     
    }

    public function student_additional_fee_report (Request $request)
    {
        $Students = Student::with(['grade', 
                                    'section', 
                                    'tuition' => function ($query) {
                                        $query->where('status', 1);
                                    },
                                    'additional_fee',
                                    'additional_fee_payment'
                                ])
                                ->where('status', 1)
                                ->where(function ($query) use ($request) {
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
                                ->orderBy('grade_id', 'ASC')
                                ->orderBy('last_name', 'ASC')
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
        
        $paid_count         = 0;
        $unpaid_count       = 0;
        $all_total_fees         = 0;
        $all_total_received     = 0;
        $all_total_receivables  = 0;
        foreach ($Students as $student)
        {
            $total_additional_fee = 0;
            $total_additional_payment = 0;
            if ($student->additional_fee)
            {
                foreach ($student->additional_fee as $additional_fee)
                {
                    $total_additional_fee   += $additional_fee->additional_amount;
                }
                $all_total_fees += $total_additional_fee;
            }

            if ($student->additional_fee_payment)
            {
                $total_additional_payment   += $student->additional_fee_payment->books;
                $total_additional_payment   += $student->additional_fee_payment->speech_lab;
                $total_additional_payment   += $student->additional_fee_payment->pe_uniform;
                $total_additional_payment   += $student->additional_fee_payment->school_uniform;
            }
            
            $all_total_received += $total_additional_payment;

            if ($total_additional_payment >= $total_additional_fee)
            {
                $paid_count++;
            }
            else
            {
                $unpaid_count++;
            }
        }
        
        $all_total_receivables  = $all_total_fees - $all_total_received;

        
        $pdf = PDF::loadView('cashier.student_additional_payment.report.student_additional_fee_report', ['Students' => $Students, 'selected_grade' => $selected_grade, 'selected_section' => $selected_section, 'paid_count' => $paid_count, 'unpaid_count' => $unpaid_count,'all_total_receivables' => $all_total_receivables, 'all_total_fees' => $all_total_fees, 'all_total_received' => $all_total_received])->setPaper('letter', 'landscape');
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf ->get_canvas();
        $canvas->page_text(5, 5, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 7, array(0, 0, 0)); //750
        return $pdf->stream();
    }
}
