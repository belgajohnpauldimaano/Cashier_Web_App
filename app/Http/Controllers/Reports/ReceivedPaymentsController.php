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

class ReceivedPaymentsController extends Controller
{
    public function index ()
    {
        $StudentPaymentLog = StudentPaymentLog::with(['student', 'user'])->paginate(10);
        $payment_sum = StudentPaymentLog::selectRaw('SUM(payment) as sum')->first();

        $Grade = Grade::all();
        $Section = Section::where('grade_id', 1)->get();
        // return json_encode($StudentPaymentLog);
        return view('reports.received_payments.index', ['StudentPaymentLog' => $StudentPaymentLog, 'Grade' => $Grade, 'Section' => $Section, 'payment_sum' => $payment_sum]);
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
        $StudentPaymentLog = StudentPaymentLog::with(['student', 'student.grade', 'student.section', 'user'])
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

        return view('reports.received_payments.partials.list', ['StudentPaymentLog' => $StudentPaymentLog, 'payment_sum' => $payment_sum, 'request' => $request->all()])->render();
    }

    public function export_pdf_received_payments (Request $request)
    {

        $StudentPaymentLog = StudentPaymentLog::with(['student', 'student.grade', 'student.section', 'user'])
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
            $StudentPaymentLog = StudentPaymentLog::join('students', 'students.id', '=', 'student_payment_logs.student_id')
                                                    ->where(function ($query) use ($grd) {
                                                        $query->whereRaw('students.grade_id = ' . $grd->id);
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
                                                    ->selectRaw('
                                                        SUM(payment) as total_payment, students.grade_id
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
}
