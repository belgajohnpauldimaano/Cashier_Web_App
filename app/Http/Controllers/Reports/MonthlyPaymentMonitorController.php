<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use PDF;

use App\Student;
use App\Grade;
use App\Section;
use App\StudentTuitionFee;
use App\SchoolYear;
use App\StudentSchoolYearTag;

class MonthlyPaymentMonitorController extends Controller
{
    public function index ()
    {
        $SchoolYear = SchoolYear::first(); 
        $sy_id = '';
        if ($SchoolYear)
        {
            $sy_id = $SchoolYear->id;
        }
        $StudentTuitionFee = StudentTuitionFee::with(['student'])->where('school_year_id', $sy_id)->paginate(10);

        $select_columns = 'down_payment, monthly_payment, total_payment, total_remaining, fully_paid, student_id, month_1_payment as m1, month_2_payment as m2, month_3_payment as m3, month_4_payment as m4, month_5_payment as m5, month_6_payment as m6, month_7_payment as m7, month_8_payment as m8, month_9_payment as m9, month_10_payment as m10';
        $month_array = [',month_1_payment as m1', ',month_2_payment as m2', ',month_3_payment as m3', ',month_4_payment as m4', ',month_5_payment as m5', ',month_6_payment as m6', ',month_7_payment as m7', ',month_8_payment as m8', ',month_9_payment as m9', ',month_10_payment as m10'];
        
        
        // $Student = Student::with([
        //                             'grade', 
        //                             'section', 
        //                             'tuition' => function ($query) use ($select_columns, $sy_id) {
        //                                 $query->selectRaw($select_columns);
        //                                 $query->where('school_year_id', $sy_id);
        //                                 $query->where('status', 1);
        //                             },
        //                             'grade.tuition_fee' => function ($query) use ($sy_id) {
        //                                 $query->where('school_year_id', $sy_id);
        //                                 $query->where('status', 1);
        //                             },
        //                             'discount_list',
        //                             'grade_tuition',
        //                             'additional_fee'
        //                         ])
        //                         // ->where('grade_id', 1)
        //                         // ->where('section_id', 1)
        //                         // ->where('status', 1)
        //                         ->where(function ($query) {
        //                             // $query->where('section_id', 1);
        //                         })
        //                         ->whereHas('tuition', function ($query) use ($sy_id) {
        //                             $query->where('school_year_id', $sy_id);
        //                         })
        //                         ->orderBy('grade_id', 'ASC')
        //                         ->paginate(10);
                                
        $Student = StudentSchoolYearTag::with([
            'student_info',
            'grade', 
            'section', 
            'tuition' => function ($query) use ($select_columns, $sy_id) {
                $query->selectRaw($select_columns);
                $query->where('school_year_id', $sy_id);
                $query->where('status', 1);
            },
            'grade.tuition_fee' => function ($query) use ($sy_id) {
                $query->where('school_year_id', $sy_id);
                $query->where('status', 1);
            },
            'discount_list' => function ($query) use ($sy_id) {
                $query->where('school_year_id', $sy_id);
            },
            'grade_tuition' => function ($query) use ($sy_id) {
                $query->where('school_year_id', $sy_id);
            },
            'additional_fee' => function ($query) use ($sy_id) {
                $query->where('school_year_id', $sy_id);
            }
        ])
        // ->where('grade_id', 1)
        // ->where('section_id', 1)
        // ->where('status', 1)
        ->where(function ($query) {
            // $query->where('section_id', 1);
        })
        ->whereHas('tuition', function ($query) use ($sy_id) {
            $query->where('school_year_id', $sy_id);
        })
        ->where('school_year_id', $sy_id)
        ->orderBy('grade_id', 'ASC')
        ->paginate(10);
        // return json_encode($Student);
        $Grade = Grade::all();
        $Section = Section::where('grade_id', 1)->get();
                
        $months_array = [
                            'June', 'July', 
                            'August',  'September', 
                            'October', 'November',
                            'December', 'January',
                            'February', 'March'];
        $month_field = [
            'm1',
            'm2',
            'm3',
            'm4',
            'm5',
            'm6',
            'm7',
            'm8',
            'm9',
            'm10',
        ];
        $SchoolYear = SchoolYear::all();
        return view('reports.monthly_payment_monitor.index', ['StudentTuitionFee' => $StudentTuitionFee, 'Grade' => $Grade, 'Section' => $Section, 'months_array' => $months_array, 'Students' => $Student, 'month_field' => $month_field, 'SchoolYear' => $SchoolYear]);
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

        $select_columns = 'down_payment, monthly_payment, total_payment, total_remaining, fully_paid, student_id, month_1_payment as m1, month_2_payment as m2, month_3_payment as m3, month_4_payment as m4, month_5_payment as m5, month_6_payment as m6, month_7_payment as m7, month_8_payment as m8, month_9_payment as m9, month_10_payment as m10';
        $month_array = [',month_1_payment as m1', ',month_2_payment as m2', ',month_3_payment as m3', ',month_4_payment as m4', ',month_5_payment as m5', ',month_6_payment as m6', ',month_7_payment as m7', ',month_8_payment as m8', ',month_9_payment as m9', ',month_10_payment as m10'];
        // $selected_months = '';
        
        // if ($request->filter_month != '' && $request->filter_month_to != '')
        // {
        //     for($i=$request->filter_month - 1;$i< $request->filter_month_to;$i++)
        //     {
        //         $selected_months .= $month_array[$i];
        //     }
        //     $select_columns = 'down_payment, monthly_payment, total_payment, total_remaining, fully_paid, student_id '. $selected_months;
        // }
        // $Student = Student::with([
        //                         'grade', 
        //                         'section', 
        //                         'tuition' => function ($query) use ($select_columns, $request) {
        //                             $query->selectRaw($select_columns);
        //                             $query->where('school_year_id', $request->filter_school_year);
        //                             $query->where('status', 1);
        //                         },
        //                         'grade.tuition_fee' => function ($query) use ($request) {
        //                             $query->where('school_year_id', $request->filter_school_year);
        //                             $query->where('status', 1);
        //                         },
        //                         'discount_list',
        //                         'grade_tuition',
        //                         'additional_fee'
        //                         ])
        //                         ->where(function ($query) use ($request) {
        //                             $query->whereRaw("concat(first_name, ' ', middle_name , ' ', last_name) like '%". $request->search_filter ."%' ");
                                                
        //                             if ($request->filter_grade)
        //                             {
        //                                 $query->where('grade_id', $request->filter_grade);
        //                             }

        //                             if ($request->filter_section)
        //                             {
        //                                 $query->where('section_id', $request->filter_section);
        //                             }
        //                         })
        //                         // ->where('status', 1)
        //                         ->whereHas('tuition', function ($query) use ($request) {
        //                             $query->where('school_year_id', $request->filter_school_year);
        //                         })
        //                         ->orderBy('grade_id', 'ASC')
        //                         ->paginate($pages);
        $Student = StudentSchoolYearTag::with([
            'student_info',
            'grade', 
            'section', 
            'tuition' => function ($query) use ($select_columns, $request) {
                $query->selectRaw($select_columns);
                $query->where('school_year_id', $request->filter_school_year);
                $query->where('status', 1);
            },
            'grade.tuition_fee' => function ($query) use ($request) {
                $query->where('school_year_id', $request->filter_school_year);
                $query->where('status', 1);
            },
            'discount_list' => function ($query) use ($request) {
                $query->where('school_year_id', $request->filter_school_year);
            },
            'grade_tuition' => function ($query) use ($request) {
                $query->where('school_year_id', $request->filter_school_year);
            },
            'additional_fee' => function ($query) use ($request) {
                $query->where('school_year_id', $request->filter_school_year);
            },
            ])
            ->where(function ($query) use ($request) {
                // $query->whereRaw("concat(first_name, ' ', middle_name , ' ', last_name) like '%". $request->search_filter ."%' ");
                            
                if ($request->filter_grade)
                {
                    $query->where('grade_id', $request->filter_grade);
                }

                if ($request->filter_section)
                {
                    $query->where('section_id', $request->filter_section);
                }
            })
            // ->where('status', 1)
            ->whereHas('tuition', function ($query) use ($request) {
                $query->where('school_year_id', $request->filter_school_year);
            })
            ->whereHas('student_info', function ($query) use ($request) {
                $query->whereRaw("concat(first_name, ' ', middle_name , ' ', last_name) like '%". $request->search_filter ."%' ");
            })
            ->where('school_year_id', $request->filter_school_year)
            ->orderBy('grade_id', 'ASC')
            ->paginate($pages);
                                
        $months_array = [
                            'June', 'July', 
                            'August',  'September', 
                            'October', 'November',
                            'December', 'January',
                            'February', 'March'];
        $month_field = [
            'm1',
            'm2',
            'm3',
            'm4',
            'm5',
            'm6',
            'm7',
            'm8',
            'm9',
            'm10',
        ];
        // return json_encode($Student);
        return view('reports.monthly_payment_monitor.partials.list', ['request' => $request->all(), 'months_array' => $months_array, 'Students' => $Student, 'month_field' => $month_field])->render();
    }

    public function export_pdf_monthly_payment_monitor (Request $request)
    {

        $select_columns = 'down_payment, monthly_payment, total_payment, total_remaining, fully_paid, student_id, month_1_payment as m1, month_2_payment as m2, month_3_payment as m3, month_4_payment as m4, month_5_payment as m5, month_6_payment as m6, month_7_payment as m7, month_8_payment as m8, month_9_payment as m9, month_10_payment as m10';
        $month_array = [',month_1_payment as m1', ',month_2_payment as m2', ',month_3_payment as m3', ',month_4_payment as m4', ',month_5_payment as m5', ',month_6_payment as m6', ',month_7_payment as m7', ',month_8_payment as m8', ',month_9_payment as m9', ',month_10_payment as m10'];
        
        
        // $Student = Student::with([
        //                         'grade', 
        //                         'section', 
        //                         'tuition' => function ($query) use ($select_columns, $request) {
        //                             $query->selectRaw($select_columns);
        //                             $query->where('school_year_id', $request->report_filter_school_year);
        //                             $query->where('status', 1);
        //                         },
        //                         'grade.tuition_fee' => function ($query) use ($request) {
        //                             $query->where('school_year_id', $request->report_filter_school_year);
        //                             $query->where('status', 1);
        //                         },
        //                         'discount_list',
        //                         'grade_tuition',
        //                         'additional_fee'
        //                         ])
        //                         ->where(function ($query) use ($request) {
        //                             $query->whereRaw("concat(first_name, ' ', middle_name , ' ', last_name) like '%". $request->report_search_filter ."%' ");
                                                
        //                             if ($request->report_filter_grade)
        //                             {
        //                                 $query->where('grade_id', $request->report_filter_grade);
        //                             }

        //                             if ($request->report_filter_section)
        //                             {
        //                                 $query->where('section_id', $request->report_filter_section);
        //                             }
        //                         })
        //                         ->whereHas('tuition', function ($query) use ($request) {
        //                             $query->where('school_year_id', $request->report_filter_school_year);
        //                         })
        //                         // ->where('status', 1)
        //                         ->orderBy('grade_id', 'ASC')
        //                         ->get();
        
        $Student = StudentSchoolYearTag::with([
            'student_info',
            'grade', 
            'section', 
            'tuition' => function ($query) use ($select_columns, $request) {
                $query->selectRaw($select_columns);
                $query->where('school_year_id', $request->report_filter_school_year);
                $query->where('status', 1);
            },
            'grade.tuition_fee' => function ($query) use ($request) {
                $query->where('school_year_id', $request->report_filter_school_year);
                $query->where('status', 1);
            },
            'discount_list' => function ($query) use ($request) {
                $query->where('school_year_id', $request->report_filter_school_year);
            },
            'grade_tuition' => function ($query) use ($request) {
                $query->where('school_year_id', $request->report_filter_school_year);
            },
            'additional_fee' => function ($query) use ($request) {
                $query->where('school_year_id', $request->report_filter_school_year);
            },
            ])
            ->where('school_year_id', $request->report_filter_school_year)
            ->where(function ($query) use ($request) {
                // $query->whereRaw("concat(first_name, ' ', middle_name , ' ', last_name) like '%". $request->report_search_filter ."%' ");
                            
                if ($request->report_filter_grade)
                {
                    $query->where('grade_id', $request->report_filter_grade);
                }

                if ($request->report_filter_section)
                {
                    $query->where('section_id', $request->report_filter_section);
                }
            })
            ->whereHas('tuition', function ($query) use ($request) {
                $query->where('school_year_id', $request->report_filter_school_year);
            })
            ->whereHas('student_info', function ($query) use ($request) {
                $query->whereRaw("concat(first_name, ' ', middle_name , ' ', last_name) like '%". $request->report_search_filter ."%' ");
            })
            // ->where('status', 1)
            ->orderBy('grade_id', 'ASC')
            ->get();
                                
        $months_array = [
                            'June', 'July', 
                            'August',  'September', 
                            'October', 'November',
                            'December', 'January',
                            'February', 'March'];
        $month_field = [
            'm1',
            'm2',
            'm3',
            'm4',
            'm5',
            'm6',
            'm7',
            'm8',
            'm9',
            'm10',
        ];
        // return json_encode($Student);
        $pdf = PDF::loadView('reports.monthly_payment_monitor.report.pdf_monthly_payment_monitor', [ 'request' => $request->all(), 'months_array' => $months_array, 'Students' => $Student, 'month_field' => $month_field])->setPaper('letter', 'landscape');
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf ->get_canvas();
        $canvas->page_text(5, 5, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 7, array(0, 0, 0));
        return $pdf->stream();
    }

    public function export_pdf_monthly_payment_summary_monitor (Request $request)
    {
        $select_columns = 'down_payment, monthly_payment, total_payment, total_remaining, fully_paid, student_id, month_1_payment as m1, month_2_payment as m2, month_3_payment as m3, month_4_payment as m4, month_5_payment as m5, month_6_payment as m6, month_7_payment as m7, month_8_payment as m8, month_9_payment as m9, month_10_payment as m10';
        $month_array_field = ['month_1_payment', 'month_2_payment', 'month_3_payment', 'month_4_payment', 'month_5_payment', 'month_6_payment', 'month_7_payment', 'month_8_payment', 'month_9_payment', 'month_10_payment'];
        $selected_months = [];
        $selected_mons_cols = '';
        $months_array = [
            'June', 'July', 
            'August',  'September', 
            'October', 'November',
            'December', 'January',
            'February', 'March'];
        $month_field = [
            'm1',
            'm2',
            'm3',
            'm4',
            'm5',
            'm6',
            'm7',
            'm8',
            'm9',
            'm10',
        ];
        $mon_from = ($request->report_filter_month ? $request->report_filter_month : 1);
        $mon_to = ($request->report_filter_month_to ? $request->report_filter_month_to : 10);

        $mon_str_dispay = 'From the month of ' . $months_array[$mon_from - 1] . ' to ' . $months_array[$mon_to - 1];
        for($i=$mon_from; $i<=$mon_to; $i++)
        // for($i=1; $i<=10; $i++)
        {
            $selected_months[] = $month_array_field[$i - 1];
            $selected_mons_cols .= 'SUM('. $month_array_field[$i - 1] .') as ' . $month_array_field[$i - 1] . ($i < $mon_to ? ',' : '');

        }

        $grade_balance = [];
        $student_per_grade = [];
        $student_discount  = [];
        $Grade = Grade::where(function ($query) use ($request) {
                            if ($request->report_filter_grade)
                            {
                                $query->where('id', $request->report_filter_grade);
                            }

                        })
                        ->get(['id', 'grade']);

        foreach ($Grade as $grd)
        {
            $StudentTuitionFee = StudentTuitionFee::join('student_school_year_tags', 'student_school_year_tags.student_id', '=', 'student_tuition_fees.student_id')
            ->join('tuition_fees', 'tuition_fees.grade_id', 'student_school_year_tags.grade_id')
            ->join('student_discount_lists', 'student_discount_lists.student_id', 'student_school_year_tags.id')
            ->where(function ($query) use ($grd, $request) {

                $query->whereRaw('tuition_fees.school_year_id = ' . $request->report_filter_school_year);
                $query->whereRaw('student_school_year_tags.school_year_id = ' . $request->report_filter_school_year);
                $query->whereRaw('student_school_year_tags.grade_id = ' . $grd->id);
                $query->whereRaw('student_school_year_tags.status = 1');
            })
            ->selectRaw('student_school_year_tags.grade_id, ' . $selected_mons_cols . ', SUM(down_payment) as total_dp, SUM(total_payment) as total_payment, SUM(tuition_fees.tuition_fee) as tuition_fee, SUM(tuition_fees.misc_fee) as misc_fee,
                SUM(tuition_fees.upon_enrollment) as upon_enrollment,  
                SUM(student_discount_lists.scholar) as total_scholar,
                tuition_fees.school_year_id
            ')
            //
                // , 
                // SUM(student_discount_lists.school_subsidy) as school_subsidy,
                // SUM(student_discount_lists.employee_scholar) as employee_scholar,
                // SUM(student_discount_lists.gov_subsidy) as gov_subsidy,
                // SUM(student_discount_lists.acad_scholar) as acad_scholar,
                // SUM(student_discount_lists.family_member) as family_member,
                // SUM(student_discount_lists.nbi_alumni) as nbi_alumni,
                // SUM(student_discount_lists.cash_discount) as cash_discount,
                // SUM(student_discount_lists.cwoir_discount) as cwoir_discount,
                // SUM(student_discount_lists.st_joseph_discount) as st_joseph_discount
            ->first();
            
            $Student = StudentSchoolYearTag::with([
                                'student_info',
                                'tuition' => function ($query) use ($select_columns, $request) {
                                    $query->selectRaw($select_columns);
                                    $query->where('school_year_id', $request->report_filter_school_year);
                                    $query->where('status', 1);
                                },
                                'grade.tuition_fee' => function ($query) use ($request) {
                                    $query->where('school_year_id', $request->report_filter_school_year);
                                    $query->where('status', 1);
                                },
                                'discount_list' => function ($query) use ($request) {
                                    $query->where('school_year_id', $request->report_filter_school_year);
                                },
                                'grade_tuition' => function ($query) use ($request) {
                                    $query->where('school_year_id', $request->report_filter_school_year);
                                },
                                'additional_fee' => function ($query) use ($request) {
                                    $query->where('school_year_id', $request->report_filter_school_year);
                                },
                                ])
                                ->whereHas('tuition', function ($query) use ($request) {
                                    $query->where('school_year_id', $request->report_filter_school_year);
                                })
                                ->where(function ($query) use ($grd) {
                                    $query->whereRaw('student_school_year_tags.grade_id = ' . $grd->id);
                                })
                                ->where('status', 1)
                                ->get();

            $student_per_grade[] = $Student;
            if ($StudentTuitionFee->grade_id != NULL)
            {
                $discount = 0;
                $discountScholar = 0;
                $discountSubsidy = 0;
                $discountEmpScholar = 0;
                $discountGovSubsidy = 0;
                $discountAcad = 0;
                $discountFamily = 0;
                $discountNBI = 0;
                $discountCash = 0;
                $discountChoir = 0;
                $discountJoseph = 0;
    
                foreach ($Student as $student)
                {
                    $tuition = $student->grade_tuition[0]->tuition_fee; 
                    $discount += ($student->discount_list->scholar != 0 ? $student->discount_list->scholar * $tuition : 0);
                    $discount += ($student->discount_list->school_subsidy != 0 ? $student->discount_list->school_subsidy : 0);
                    $discount += ($student->discount_list->employee_scholar != 0 ? $student->discount_list->employee_scholar * $tuition : 0);
                    $discount += ($student->discount_list->gov_subsidy  != 0 ? $student->discount_list->gov_subsidy  : 0);
                    $discount += ($student->discount_list->acad_scholar  != 0 ? $student->discount_list->acad_scholar * $tuition : 0);
                    $discount += ($student->discount_list->family_member  != 0 ? $student->discount_list->family_member * $tuition : 0);
                    $discount += ($student->discount_list->nbi_alumni  != 0 ? $student->discount_list->nbi_alumni * $tuition : 0);
                    $discount += ($student->discount_list->cash_discount  != 0 ? $student->discount_list->cash_discount * $tuition : 0);
                    $discount += ($student->discount_list->cwoir_discount  != 0 ? $student->discount_list->cwoir_discount * $tuition : 0);
                    $discount += ($student->discount_list->st_joseph_discount  != 0 ? $student->discount_list->st_joseph_discount : 0);
                    
                    $discountScholar += ($student->discount_list->scholar != 0 ? $student->discount_list->scholar * $tuition : 0);
                    $discountSubsidy += ($student->discount_list->school_subsidy != 0 ? $student->discount_list->school_subsidy : 0);
                    $discountEmpScholar += ($student->discount_list->employee_scholar != 0 ? $student->discount_list->employee_scholar * $tuition : 0);
                    $discountGovSubsidy += ($student->discount_list->gov_subsidy  != 0 ? $student->discount_list->gov_subsidy  : 0);
                    $discountAcad += ($student->discount_list->acad_scholar  != 0 ? $student->discount_list->acad_scholar * $tuition : 0);
                    $discountFamily += ($student->discount_list->family_member  != 0 ? $student->discount_list->family_member * $tuition : 0);
                    $discountNBI += ($student->discount_list->nbi_alumni  != 0 ? $student->discount_list->nbi_alumni * $tuition : 0);
                    $discountCash += ($student->discount_list->cash_discount  != 0 ? $student->discount_list->cash_discount * $tuition : 0);
                    $discountChoir += ($student->discount_list->cwoir_discount  != 0 ? $student->discount_list->cwoir_discount * $tuition : 0);
                    $discountJoseph += ($student->discount_list->st_joseph_discount  != 0 ? $student->discount_list->st_joseph_discount : 0);
                }
                
                $scholar_discount[] = $discountScholar;
                $subsidy_discount[] = $discountSubsidy;
                $emp_discount[] = $discountEmpScholar;
                $gov_discount[] = $discountGovSubsidy;
                $acad_discount[] = $discountAcad;
                $family_discount[] = $discountFamily;
                $nbi_discount[] = $discountNBI;
                $cash_discount[] = $discountCash;
                $choir_discount[] = $discountChoir;
                $joseph_discount[] = $discountJoseph;
                $student_discount[] = $discount;
                $grade_balance[] = $StudentTuitionFee;
            }
        }
        // $student_discount = $student_discount;

        // echo $student_discount->where('grd_id', 1)->first();
        // return json_encode([$request->all()]);
         $pdf = PDF::loadView('reports.monthly_payment_monitor.report.pdf_monthly_payment_summary_monitor', [ 'request' => $request->all(), 'grade_balance' => $grade_balance ,'months_array' => $months_array, 'mon_str_dispay' => $mon_str_dispay, 'month_array_field' => $month_array_field, 'mon_from' => $mon_from, 'mon_to' => $mon_to, 'Grade' => $Grade, 'month_field' => $month_field, 'student_discount' => $student_discount, 'scholar_discount' => $scholar_discount, 'subsidy_discount' => $subsidy_discount, 'emp_discount' => $emp_discount, 'gov_discount' => $gov_discount, 'acad_discount' => $acad_discount, 'family_discount' => $family_discount, 'nbi_discount' => $nbi_discount, 'cash_discount' => $cash_discount, 'choir_discount' => $choir_discount, 'joseph_discount' => $joseph_discount, ])->setPaper('letter', 'landscape');

        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf ->get_canvas();
        $canvas->page_text(5, 5, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 7, array(0, 0, 0));
        return $pdf->stream();
        $select_columns = 'down_payment, monthly_payment, total_payment, total_remaining, fully_paid, student_id, month_1_payment as m1, month_2_payment as m2, month_3_payment as m3, month_4_payment as m4, month_5_payment as m5, month_6_payment as m6, month_7_payment as m7, month_8_payment as m8, month_9_payment as m9, month_10_payment as m10';
        $month_array = [',month_1_payment as m1', ',month_2_payment as m2', ',month_3_payment as m3', ',month_4_payment as m4', ',month_5_payment as m5', ',month_6_payment as m6', ',month_7_payment as m7', ',month_8_payment as m8', ',month_9_payment as m9', ',month_10_payment as m10'];
        
        $Student = Student::with([
                                'grade', 
                                'section', 
                                'tuition' => function ($query) use ($select_columns, $request) {
                                    $query->selectRaw($select_columns);
                                    $query->where('school_year_id', $request->report_filter_school_year);
                                    $query->where('status', 1);
                                },
                                'grade.tuition_fee' => function ($query) use ($request) {
                                    $query->where('status', 1);
                                    $query->where('school_year_id', $request->report_filter_school_year);
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
                                ->whereHas('tuition', function ($query) use ($request) {
                                    $query->where('school_year_id', $request->report_filter_school_year);
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
        $month_field = [
            'm1',
            'm2',
            'm3',
            'm4',
            'm5',
            'm6',
            'm7',
            'm8',
            'm9',
            'm10',
        ];

        $pdf = PDF::loadView('reports.monthly_payment_monitor.report.pdf_monthly_payment_summary_monitor', [ 'request' => $request->all(), 'months_array' => $months_array, 'Students' => $Student, 'month_field' => $month_field])->setPaper('letter', 'portrait');

        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf ->get_canvas();
        $canvas->page_text(5, 5, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 7, array(0, 0, 0));
        return $pdf->stream();
    }

    
    public function export_pdf_monthly_payment_monitor_teacher (Request $request)
    {

        
        $select_columns = 'down_payment, monthly_payment, total_payment, total_remaining, fully_paid, student_id, month_1_payment as m1, month_2_payment as m2, month_3_payment as m3, month_4_payment as m4, month_5_payment as m5, month_6_payment as m6, month_7_payment as m7, month_8_payment as m8, month_9_payment as m9, month_10_payment as m10';
        $month_array = [',month_1_payment as m1', ',month_2_payment as m2', ',month_3_payment as m3', ',month_4_payment as m4', ',month_5_payment as m5', ',month_6_payment as m6', ',month_7_payment as m7', ',month_8_payment as m8', ',month_9_payment as m9', ',month_10_payment as m10'];
        
        
        // $Student = Student::with([
        //                         'grade', 
        //                         'section', 
        //                         'tuition' => function ($query) use ($select_columns, $request) {
        //                             $query->selectRaw($select_columns);
        //                             $query->where('school_year_id', $request->report_filter_school_year);
        //                             $query->where('status', 1);
        //                         },
        //                         'grade.tuition_fee' => function ($query) use($request) {
        //                             $query->where('status', 1);
        //                             $query->where('school_year_id', $request->report_filter_school_year);
        //                         },
        //                         'discount_list',
        //                         'grade_tuition',
        //                         'additional_fee'
        //                         ])
        //                         ->where(function ($query) use ($request) {
        //                             $query->whereRaw("concat(first_name, ' ', middle_name , ' ', last_name) like '%". $request->report_search_filter ."%' ");
                                                
        //                             if ($request->report_filter_grade)
        //                             {
        //                                 $query->where('grade_id', $request->report_filter_grade);
        //                             }

        //                             if ($request->report_filter_section)
        //                             {
        //                                 $query->where('section_id', $request->report_filter_section);
        //                             }
        //                         })
        //                         ->whereHas('tuition', function ($query) use ($request) {
        //                             $query->where('school_year_id', $request->report_filter_school_year);
        //                         })
        //                         ->where('status', 1)
        //                         ->orderBy('grade_id', 'ASC')
        //                         ->get();
        
        
        $Student = StudentSchoolYearTag::with([
            'student_info',
            'grade', 
            'section', 
            'tuition' => function ($query) use ($select_columns, $request) {
                $query->selectRaw($select_columns);
                $query->where('school_year_id', $request->report_filter_school_year);
                $query->where('status', 1);
            },
            'grade.tuition_fee' => function ($query) use($request) {
                $query->where('status', 1);
                $query->where('school_year_id', $request->report_filter_school_year);
            },
            'discount_list' => function ($query) use($request) {
                $query->where('school_year_id', $request->report_filter_school_year);
            },
            'grade_tuition' => function ($query) use($request) {
                $query->where('school_year_id', $request->report_filter_school_year);
            },
            'additional_fee' => function ($query) use($request) {
                $query->where('school_year_id', $request->report_filter_school_year);
            },
            ])
            ->where(function ($query) use ($request) {
                // $query->whereRaw("concat(first_name, ' ', middle_name , ' ', last_name) like '%". $request->report_search_filter ."%' ");
                            
                if ($request->report_filter_grade)
                {
                    $query->where('grade_id', $request->report_filter_grade);
                }

                if ($request->report_filter_section)
                {
                    $query->where('section_id', $request->report_filter_section);
                }
            })
            ->whereHas('tuition', function ($query) use ($request) {
                $query->where('school_year_id', $request->report_filter_school_year);
            })
            ->whereHas('student_info', function ($query) use ($request) {
                $query->whereRaw("concat(first_name, ' ', middle_name , ' ', last_name) like '%". $request->report_search_filter ."%' ");
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
        $month_field = [
            'm1',
            'm2',
            'm3',
            'm4',
            'm5',
            'm6',
            'm7',
            'm8',
            'm9',
            'm10',
        ];
        // return json_encode($Student);
        $pdf = PDF::loadView('reports.monthly_payment_monitor.report.pdf_monthly_payment_monitor_teacher', [ 'request' => $request->all(), 'months_array' => $months_array, 'Students' => $Student, 'month_field' => $month_field])->setPaper('legal', 'portrait');
        $pdf->output();
        $dom_pdf = $pdf->getDomPDF();
        $canvas = $dom_pdf ->get_canvas();
        $canvas->page_text(5, 5, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 7, array(0, 0, 0));
        return $pdf->stream();
    }

    
}
