<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use PDF;

use App\Student;
use App\Grade;
use App\Section;
use App\StudentTuitionFee;

class MonthlyPaymentMonitorController extends Controller
{
    public function index ()
    {
        $StudentTuitionFee = StudentTuitionFee::with(['student'])->paginate(10);

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
                                ->orderBy('grade_id', 'ASC')
                                ->paginate(10);

        $Grade = Grade::all();
        $Section = Section::where('grade_id', 1)->get();
                
        $months_array = [
                            'June', 'July', 
                            'August',  'September', 
                            'October', 'November',
                            'December', 'January',
                            'February', 'March'];
        // return json_encode([$Student]);
        return view('reports.monthly_payment_monitor.index', ['StudentTuitionFee' => $StudentTuitionFee, 'Grade' => $Grade, 'Section' => $Section, 'months_array' => $months_array, 'Students' => $Student]);
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

        $select_columns = 'down_payment, monthly_payment, total_payment, total_remaining, fully_paid, student_id, month_1_payment, month_2_payment, month_3_payment, month_4_payment, month_5_payment, month_6_payment, month_7_payment, month_8_payment, month_9_payment, month_10_payment';

        if ($request->filter_month)
        {
            $month_array = ['month_1_payment', 'month_2_payment', 'month_3_payment', 'month_4_payment', 'month_5_payment', 'month_6_payment', 'month_7_payment', 'month_8_payment', 'month_9_payment', 'month_10_payment'];
            $select_columns = 'down_payment, monthly_payment, total_payment, total_remaining, fully_paid, student_id, '. $month_array[$request->filter_month - 1] .' as month';
        }

        $Student = Student::with([
                                'grade', 
                                'section', 
                                'tuition' => function ($query) use ($select_columns) {
                                    $query->selectRaw($select_columns);
                                    $query->where('status', 1);
                                },
                                'grade.tuition_fee' => function ($query) {
                                    $query->where('status', 1);
                                },
                                'discount_list',
                                'grade_tuition',
                                'additional_fee'
                                ])
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
                                ->where('status', 1)
                                ->orderBy('grade_id', 'ASC')
                                ->paginate($pages);
        $months_array = [
                            'June', 'July', 
                            'August',  'September', 
                            'October', 'November',
                            'December', 'January',
                            'February', 'March'];
        // return json_encode($Student);
        return view('reports.monthly_payment_monitor.partials.list', ['request' => $request->all(), 'months_array' => $months_array, 'Students' => $Student])->render();
    }

    public function export_pdf_monthly_payment_monitor (Request $request)
    {

        
        $select_columns = 'down_payment, monthly_payment, total_payment, total_remaining, fully_paid, student_id, month_1_payment, month_2_payment, month_3_payment, month_4_payment, month_5_payment, month_6_payment, month_7_payment, month_8_payment, month_9_payment, month_10_payment';

        if ($request->report_filter_month)
        {
            $month_array = ['month_1_payment', 'month_2_payment', 'month_3_payment', 'month_4_payment', 'month_5_payment', 'month_6_payment', 'month_7_payment', 'month_8_payment', 'month_9_payment', 'month_10_payment'];
            $select_columns = 'down_payment, monthly_payment, total_payment, total_remaining, fully_paid, student_id, '. $month_array[$request->report_filter_month - 1] .' as month';
        }
                
        $Student = Student::with([
                                'grade', 
                                'section', 
                                'tuition' => function ($query) use ($select_columns) {
                                    $query->selectRaw($select_columns);
                                    $query->where('status', 1);
                                },
                                'grade.tuition_fee' => function ($query) {
                                    $query->where('status', 1);
                                },
                                'discount_list',
                                'grade_tuition',
                                'additional_fee'
                                ])
                                ->where(function ($query) use ($request) {
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
                                ->where('status', 1)
                                ->orderBy('grade_id', 'ASC')
                                ->get();

        $months_array = [
                            'June', 'July', 
                            'August',  'September', 
                            'October', 'November',
                            'December', 'January',
                            'February', 'March'];
        // return json_encode($StudentTuitionFee);
        $pdf = PDF::loadView('reports.monthly_payment_monitor.report.pdf_monthly_payment_monitor', [ 'request' => $request->all(), 'months_array' => $months_array, 'Students' => $Student])->setPaper('letter', 'landscape');
        return $pdf->stream();
    }
}
