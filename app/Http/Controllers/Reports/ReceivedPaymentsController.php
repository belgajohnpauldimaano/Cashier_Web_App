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
                                                $query->where('created_at', '>', date('Y-m-d H:i:s', strtotime($request->filter_start_date . ' 00:00:00')));
                                            }

                                            if ($request->filter_end_date)
                                            {
                                                $query->where('created_at', '<', date('Y-m-d H:i:s', strtotime($request->filter_end_date . ' 23:59:00')));
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
                                        ->paginate($pages);

        $payment_sum = StudentPaymentLog::where(function ($query) use ($request){
                                            
                                            if ($request->filter_start_date)
                                            {
                                                $query->where('created_at', '>', date('Y-m-d H:i:s', strtotime($request->filter_start_date . ' 00:00:00')));
                                            }

                                            if ($request->filter_end_date)
                                            {
                                                $query->where('created_at', '<', date('Y-m-d H:i:s', strtotime($request->filter_end_date . ' 23:59:00')));
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
                                        ->selectRaw('SUM(payment) as sum')->first();

        return view('reports.received_payments.partials.list', ['StudentPaymentLog' => $StudentPaymentLog, 'payment_sum' => $payment_sum, 'request' => $request->all()])->render();
    }

    public function export_pdf_received_payments (Request $request)
    {

        $StudentPaymentLog = StudentPaymentLog::with(['student', 'student.grade', 'student.section', 'user'])
                                        ->where(function ($query) use ($request){
                                            
                                            if ($request->filter_start_date)
                                            {
                                                $query->where('created_at', '>', date('Y-m-d H:i:s', strtotime($request->filter_start_date . ' 00:00:00')));
                                            }

                                            if ($request->filter_end_date)
                                            {
                                                $query->where('created_at', '<', date('Y-m-d H:i:s', strtotime($request->filter_end_date . ' 23:59:00')));
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
                                        ->get();

        $payment_sum = StudentPaymentLog::where(function ($query) use ($request){
                                            
                                            if ($request->filter_start_date)
                                            {
                                                $query->where('created_at', '>', date('Y-m-d H:i:s', strtotime($request->filter_start_date . ' 00:00:00')));
                                            }

                                            if ($request->filter_end_date)
                                            {
                                                $query->where('created_at', '<', date('Y-m-d H:i:s', strtotime($request->filter_end_date . ' 23:59:00')));
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
                                        ->selectRaw('SUM(payment) as sum')->first();

        $range_from = $request->filter_start_date;
        $range_to = $request->filter_end_date;
        // return json_encode($Students);
        // PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        $pdf = PDF::loadView('reports.received_payments.report.pdf_receivedpayments', ['StudentPaymentLog' => $StudentPaymentLog, 'payment_sum' => $payment_sum, 'range_from' => $range_from, 'range_to' => $range_to])->setPaper('letter', 'landscape');
        return $pdf->stream();
        // return $pdf->download('Balance-Summary-List.pdf'

        return view('reports.received_payments.report.pdf_receivedpayments', ['StudentPaymentLog' => $StudentPaymentLog, 'payment_sum' => $payment_sum, 'range_from' => $range_from, 'range_to' => $range_to]);
    }
}
