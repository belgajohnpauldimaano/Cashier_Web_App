<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\StudentSchoolYearTag;
use App\Grade;
use App\SchoolYear;
use App\Section;

class StudentDiscountList extends Controller
{
    public function index () 
    {
        $SchoolYear = SchoolYear::first();
        $Students  = StudentSchoolYearTag::join('students', 'students.id', '=', 'student_school_year_tags.student_id')
        ->join('student_discount_lists', function ($join) use ($SchoolYear) {
            $join->on('student_discount_lists.student_id', '=', 'student_school_year_tags.student_id');
            // ->where('student_discount_lists.school_year_id', $SchoolYear->id);
        })
        ->join('grades', 'grades.id', '=', 'student_school_year_tags.grade_id')
        ->join('sections', 'sections.id', '=', 'student_school_year_tags.section_id')
        ->join('tuition_fees', function ($join) use ($SchoolYear) {
            $join->on('tuition_fees.grade_id', '=', 'student_school_year_tags.grade_id');
            // ->where('student_discount_lists.school_year_id', $SchoolYear->id);
        })
        // ->join('tuition_fees', 'tuition_fees.grade_id', '=', 'student_school_year_tags.grade_id')
        // ->where('student_school_year_tags.school_year_id', $SchoolYear->id)
        // ->where('tuition_fees.school_year_id', $SchoolYear->id)
        // ->where('student_discount_lists.school_year_id',  $SchoolYear->id)
        ->where(function ($query) {
            $query->whereRaw('
                (
                    (student_discount_lists.scholar * tuition_fees.tuition_fee) +
                    (student_discount_lists.school_subsidy) +
                    (student_discount_lists.employee_scholar * tuition_fees.tuition_fee) +
                    (student_discount_lists.acad_scholar * tuition_fees.tuition_fee) +
                    (student_discount_lists.family_member * tuition_fees.tuition_fee) +
                    (student_discount_lists.nbi_alumni * tuition_fees.tuition_fee) +
                    (student_discount_lists.cash_discount * tuition_fees.tuition_fee) +
                    (student_discount_lists.cwoir_discount * tuition_fees.tuition_fee) +
                    (student_discount_lists.st_joseph_discount) +
                    (student_discount_lists.other_discount)
                ) > 0
            ');
        })
        ->selectRaw('
            student_school_year_tags.student_id,
            student_school_year_tags.grade_id,
            student_school_year_tags.section_id,
            student_school_year_tags.school_year_id,
            students.first_name,
            students.middle_name,
            students.last_name,
            grades.grade as student_grade,
            sections.section_name,
            tuition_fees.tuition_fee,
            student_discount_lists.scholar,
            student_discount_lists.school_subsidy,
            student_discount_lists.employee_scholar,
            student_discount_lists.acad_scholar,
            student_discount_lists.family_member,
            student_discount_lists.nbi_alumni,
            student_discount_lists.cash_discount,
            student_discount_lists.cwoir_discount,
            student_discount_lists.st_joseph_discount,
            student_discount_lists.other_discount,
            (student_discount_lists.scholar * tuition_fees.tuition_fee) AS total_scholar,
            (student_discount_lists.employee_scholar * tuition_fees.tuition_fee) AS total_employee_scholar,
            (student_discount_lists.acad_scholar * tuition_fees.tuition_fee) AS total_acad_scholar,
            (student_discount_lists.family_member * tuition_fees.tuition_fee) AS total_family_member,
            (student_discount_lists.nbi_alumni * tuition_fees.tuition_fee) AS total_nbi_alumni,
            (student_discount_lists.cash_discount * tuition_fees.tuition_fee) AS total_cash_discount,
            (student_discount_lists.cwoir_discount * tuition_fees.tuition_fee) AS total_cwoir_discount,
            (
                (student_discount_lists.scholar * tuition_fees.tuition_fee) +
                (student_discount_lists.school_subsidy) +
                (student_discount_lists.employee_scholar * tuition_fees.tuition_fee) +
                (student_discount_lists.acad_scholar * tuition_fees.tuition_fee) +
                (student_discount_lists.family_member * tuition_fees.tuition_fee) +
                (student_discount_lists.nbi_alumni * tuition_fees.tuition_fee) +
                (student_discount_lists.cash_discount * tuition_fees.tuition_fee) +
                (student_discount_lists.cwoir_discount * tuition_fees.tuition_fee) +
                (student_discount_lists.st_joseph_discount) +
                (student_discount_lists.other_discount)
            )
            AS total_discount
        ')
        ->orderBy('students.last_name')
        ->paginate(10);


        $Students_sum  = StudentSchoolYearTag::join('students', 'students.id', '=', 'student_school_year_tags.student_id')
        ->join('student_discount_lists', function ($join) use ($SchoolYear) {
            $join->on('student_discount_lists.student_id', '=', 'student_school_year_tags.student_id');
            // ->where('student_discount_lists.school_year_id', $SchoolYear->id);
        })
        ->join('grades', 'grades.id', '=', 'student_school_year_tags.grade_id')
        ->join('sections', 'sections.id', '=', 'student_school_year_tags.section_id')
        ->join('tuition_fees', function ($join) use ($SchoolYear) {
            $join->on('tuition_fees.grade_id', '=', 'student_school_year_tags.grade_id');
            // ->where('student_discount_lists.school_year_id', $SchoolYear->id);
        })
        // ->join('tuition_fees', 'tuition_fees.grade_id', '=', 'student_school_year_tags.grade_id')
        // ->where('student_school_year_tags.school_year_id', $SchoolYear->id)
        // ->where('tuition_fees.school_year_id', $SchoolYear->id)
        // ->where('student_discount_lists.school_year_id',  $SchoolYear->id)
        ->where(function ($query) {
            $query->whereRaw('
                (
                    (student_discount_lists.scholar * tuition_fees.tuition_fee) +
                    (student_discount_lists.school_subsidy) +
                    (student_discount_lists.employee_scholar * tuition_fees.tuition_fee) +
                    (student_discount_lists.acad_scholar * tuition_fees.tuition_fee) +
                    (student_discount_lists.family_member * tuition_fees.tuition_fee) +
                    (student_discount_lists.nbi_alumni * tuition_fees.tuition_fee) +
                    (student_discount_lists.cash_discount * tuition_fees.tuition_fee) +
                    (student_discount_lists.cwoir_discount * tuition_fees.tuition_fee) +
                    (student_discount_lists.st_joseph_discount) +
                    (student_discount_lists.other_discount)
                ) > 0
            ');
        })
        ->selectRaw('
            SUM(
                (student_discount_lists.scholar * tuition_fees.tuition_fee) +
                (student_discount_lists.school_subsidy) +
                (student_discount_lists.employee_scholar * tuition_fees.tuition_fee) +
                (student_discount_lists.acad_scholar * tuition_fees.tuition_fee) +
                (student_discount_lists.family_member * tuition_fees.tuition_fee) +
                (student_discount_lists.nbi_alumni * tuition_fees.tuition_fee) +
                (student_discount_lists.cash_discount * tuition_fees.tuition_fee) +
                (student_discount_lists.cwoir_discount * tuition_fees.tuition_fee) +
                (student_discount_lists.st_joseph_discount) +
                (student_discount_lists.other_discount)
            )
            AS total_discount
        ')
        ->first();
        
        $Grade = Grade::all();
        $Section = Section::where('grade_id', 1)->get();
        $SchoolYear = SchoolYear::all();
        $discount_types = json_decode(json_encode(\App\Discount::DISCOUNT_TYPES));
        // return json_encode([$discount_types]);

        $total = 0 ;
        foreach ($Students as $student)
        {
            $total_discounts  = $student->total_scholar +
                                $student->school_subsidy +
                                $student->total_employee_scholar +
                                $student->total_acad_scholar +
                                $student->total_family_member +
                                $student->total_nbi_alumni +
                                $student->total_cash_discount +
                                $student->total_cwoir_discount +
                                $student->st_joseph_discount;

            $total += $total_discounts;

        }

        return view('admin.student_discounts_list.index', compact('Students', 'Grade', 'Section', 'SchoolYear', 'discount_types', 'total', 'Students_sum'))->render();
    }

    public function list_data (Request $request) 
    {
        $pages = 10;
        if ($request->show_count == '')
        {
            $pages = 10;
        }
        else
        {
            $pages = $request->show_count;
        }

        $Students  = StudentSchoolYearTag::join('students', 'students.id', '=', 'student_school_year_tags.student_id')
        ->join('student_discount_lists', function ($join) use ($request) {
            $join->on('student_discount_lists.student_id', '=', 'student_school_year_tags.student_id');
            $join->where('student_discount_lists.school_year_id', $request->filter_school_year);
        })
        ->join('grades', 'grades.id', '=', 'student_school_year_tags.grade_id')
        ->join('sections', 'sections.id', '=', 'student_school_year_tags.section_id')
        ->join('tuition_fees', function ($join) use ($request) {
            $join->on('tuition_fees.grade_id', '=', 'student_school_year_tags.grade_id');
            $join->where('student_discount_lists.school_year_id', $request->filter_school_year);
        })
        ->where(function ($query) use ($request) {
            $query->whereRaw("concat(students.first_name, ' ', students.middle_name , ' ', students.last_name) like '%". $request->search_filter ."%' ");
            if ($request->filter_grade)
            {
                $query->where('student_school_year_tags.grade_id', $request->filter_grade);
            }

            if ($request->filter_section)
            {
                $query->where('student_school_year_tags.section_id', $request->filter_section);
            }
        })
        ->where('student_discount_lists.school_year_id',  $request->filter_school_year)
        ->where('student_school_year_tags.school_year_id',  $request->filter_school_year)
        ->where(function ($query) use($request) {
            if ($request->filter_discount_type == 0)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.scholar * tuition_fees.tuition_fee) +
                        (student_discount_lists.school_subsidy) +
                        (student_discount_lists.employee_scholar * tuition_fees.tuition_fee) +
                        (student_discount_lists.acad_scholar * tuition_fees.tuition_fee) +
                        (student_discount_lists.family_member * tuition_fees.tuition_fee) +
                        (student_discount_lists.nbi_alumni * tuition_fees.tuition_fee) +
                        (student_discount_lists.cash_discount * tuition_fees.tuition_fee) +
                        (student_discount_lists.cwoir_discount * tuition_fees.tuition_fee) +
                        (student_discount_lists.st_joseph_discount)+
                        (student_discount_lists.other_discount)
                    ) > 0
                ');
            }
            else if ($request->filter_discount_type == 1)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.scholar * tuition_fees.tuition_fee)
                    ) > 0
                ');
            }
            else if ($request->filter_discount_type == 2)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.school_subsidy)
                    ) > 0
                ');
            }
            else if ($request->filter_discount_type == 3)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.employee_scholar * tuition_fees.tuition_fee)
                    ) > 0
                ');
            }
            else if ($request->filter_discount_type == 4)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.acad_scholar * tuition_fees.tuition_fee)
                    ) > 0
                ');
            }
            else if ($request->filter_discount_type == 5)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.family_member * tuition_fees.tuition_fee)
                    ) > 0
                ');
            }
            else if ($request->filter_discount_type == 6)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.nbi_alumni * tuition_fees.tuition_fee)
                    ) > 0
                ');
            }
            else if ($request->filter_discount_type == 7)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.cash_discount * tuition_fees.tuition_fee)
                    ) > 0
                ');
            }
            else if ($request->filter_discount_type == 8)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.cwoir_discount * tuition_fees.tuition_fee)
                    ) > 0
                ');
            }
            else if ($request->filter_discount_type == 9)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.st_joseph_discount)
                    ) > 0
                ');
            }
            else if ($request->filter_discount_type == 10)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.other_discount)
                    ) > 0
                ');
            }
        })
        ->selectRaw('
            student_school_year_tags.id,
            student_school_year_tags.student_id,
            student_school_year_tags.grade_id,
            student_school_year_tags.section_id,
            student_school_year_tags.school_year_id,
            students.first_name,
            students.middle_name,
            students.last_name,
            grades.grade as student_grade,
            sections.section_name,
            tuition_fees.tuition_fee,
            student_discount_lists.scholar,
            student_discount_lists.school_subsidy,
            student_discount_lists.employee_scholar,
            student_discount_lists.acad_scholar,
            student_discount_lists.family_member,
            student_discount_lists.nbi_alumni,
            student_discount_lists.cash_discount,
            student_discount_lists.cwoir_discount,
            student_discount_lists.st_joseph_discount,
            student_discount_lists.other_discount,
            (student_discount_lists.scholar * tuition_fees.tuition_fee) AS total_scholar,
            (student_discount_lists.employee_scholar * tuition_fees.tuition_fee) AS total_employee_scholar,
            (student_discount_lists.acad_scholar * tuition_fees.tuition_fee) AS total_acad_scholar,
            (student_discount_lists.family_member * tuition_fees.tuition_fee) AS total_family_member,
            (student_discount_lists.nbi_alumni * tuition_fees.tuition_fee) AS total_nbi_alumni,
            (student_discount_lists.cash_discount * tuition_fees.tuition_fee) AS total_cash_discount,
            (student_discount_lists.cwoir_discount * tuition_fees.tuition_fee) AS total_cwoir_discount,
            (
                (student_discount_lists.scholar * tuition_fees.tuition_fee) +
                (student_discount_lists.school_subsidy) +
                (student_discount_lists.employee_scholar * tuition_fees.tuition_fee) +
                (student_discount_lists.acad_scholar * tuition_fees.tuition_fee) +
                (student_discount_lists.family_member * tuition_fees.tuition_fee) +
                (student_discount_lists.nbi_alumni * tuition_fees.tuition_fee) +
                (student_discount_lists.cash_discount * tuition_fees.tuition_fee) +
                (student_discount_lists.cwoir_discount * tuition_fees.tuition_fee) +
                (student_discount_lists.st_joseph_discount) +
                (student_discount_lists.other_discount)
            )
            AS total_discount
        ')
        ->orderBy('students.last_name')
        ->paginate($pages);

        

        $Students_sum  = StudentSchoolYearTag::join('students', 'students.id', '=', 'student_school_year_tags.student_id')
        // ->join('student_discount_lists', 'student_discount_lists.student_id', '=', 'student_school_year_tags.student_id')
        ->join('student_discount_lists', function ($join) use ($request) {
            $join->on('student_discount_lists.student_id', '=', 'student_school_year_tags.student_id');
            // ->where('student_discount_lists.school_year_id', $request->filter_school_year);
        })
        ->join('grades', 'grades.id', '=', 'student_school_year_tags.grade_id')
        ->join('sections', 'sections.id', '=', 'student_school_year_tags.section_id')
        // ->join('tuition_fees', 'tuition_fees.grade_id', '=', 'student_school_year_tags.grade_id')
        ->join('tuition_fees', function ($join) use ($request) {
            $join->on('tuition_fees.grade_id', '=', 'student_school_year_tags.grade_id');
            // ->where('student_discount_lists.school_year_id', $request->filter_school_year);
        })
        ->where(function ($query) use ($request) {
            $query->whereRaw("concat(students.first_name, ' ', students.middle_name , ' ', students.last_name) like '%". $request->search_filter ."%' ");
            if ($request->filter_grade)
            {
                $query->where('student_school_year_tags.grade_id', $request->filter_grade);
            }

            if ($request->filter_section)
            {
                $query->where('student_school_year_tags.section_id', $request->filter_section);
            }
        })
        // ->where('student_school_year_tags.school_year_id',  $request->filter_school_year)
        // ->where('tuition_fees.school_year_id',  $request->filter_school_year)
        ->where('student_discount_lists.school_year_id',  $request->filter_school_year)
        ->where('student_school_year_tags.school_year_id',  $request->filter_school_year)
        ->where(function ($query) use($request) {
            if ($request->filter_discount_type == 0)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.scholar * tuition_fees.tuition_fee) +
                        (student_discount_lists.school_subsidy) +
                        (student_discount_lists.employee_scholar * tuition_fees.tuition_fee) +
                        (student_discount_lists.acad_scholar * tuition_fees.tuition_fee) +
                        (student_discount_lists.family_member * tuition_fees.tuition_fee) +
                        (student_discount_lists.nbi_alumni * tuition_fees.tuition_fee) +
                        (student_discount_lists.cash_discount * tuition_fees.tuition_fee) +
                        (student_discount_lists.cwoir_discount * tuition_fees.tuition_fee) +
                        (student_discount_lists.st_joseph_discount)
                    ) > 0
                ');
            }
            else if ($request->filter_discount_type == 1)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.scholar * tuition_fees.tuition_fee)
                    ) > 0
                ');
            }
            else if ($request->filter_discount_type == 2)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.school_subsidy)
                    ) > 0
                ');
            }
            else if ($request->filter_discount_type == 3)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.employee_scholar * tuition_fees.tuition_fee)
                    ) > 0
                ');
            }
            else if ($request->filter_discount_type == 4)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.acad_scholar * tuition_fees.tuition_fee)
                    ) > 0
                ');
            }
            else if ($request->filter_discount_type == 5)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.family_member * tuition_fees.tuition_fee)
                    ) > 0
                ');
            }
            else if ($request->filter_discount_type == 6)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.nbi_alumni * tuition_fees.tuition_fee)
                    ) > 0
                ');
            }
            else if ($request->filter_discount_type == 7)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.cash_discount * tuition_fees.tuition_fee)
                    ) > 0
                ');
            }
            else if ($request->filter_discount_type == 8)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.cwoir_discount * tuition_fees.tuition_fee)
                    ) > 0
                ');
            }
            else if ($request->filter_discount_type == 9)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.st_joseph_discount)
                    ) > 0
                ');
            }
            else if ($request->filter_discount_type == 10)
            {
                $query->whereRaw('
                    (
                        (student_discount_lists.other_discount)
                    ) > 0
                ');
            }
        })
        ->selectRaw('
            SUM(
                (student_discount_lists.scholar * tuition_fees.tuition_fee) +
                (student_discount_lists.school_subsidy) +
                (student_discount_lists.employee_scholar * tuition_fees.tuition_fee) +
                (student_discount_lists.acad_scholar * tuition_fees.tuition_fee) +
                (student_discount_lists.family_member * tuition_fees.tuition_fee) +
                (student_discount_lists.nbi_alumni * tuition_fees.tuition_fee) +
                (student_discount_lists.cash_discount * tuition_fees.tuition_fee) +
                (student_discount_lists.cwoir_discount * tuition_fees.tuition_fee) +
                (student_discount_lists.st_joseph_discount) +
                (student_discount_lists.other_discount)
            )
            AS total_discount,
            (student_discount_lists.scholar * tuition_fees.tuition_fee) AS total_scholar,
            (student_discount_lists.school_subsidy) AS total_school_subsidy,
            (student_discount_lists.employee_scholar * tuition_fees.tuition_fee) AS total_employee_scholar,
            (student_discount_lists.acad_scholar * tuition_fees.tuition_fee) AS total_acad_scholar,
            (student_discount_lists.family_member * tuition_fees.tuition_fee) AS total_family_member,
            (student_discount_lists.nbi_alumni * tuition_fees.tuition_fee) AS total_nbi_alumni,
            (student_discount_lists.cash_discount * tuition_fees.tuition_fee) AS total_cash_discount,
            (student_discount_lists.cwoir_discount * tuition_fees.tuition_fee) AS total_cwoir_discount,
            (student_discount_lists.st_joseph_discount) AS total_st_joseph_discount,
            (student_discount_lists.other_discount) AS total_other_discount
        ')
        ->first();

        $discount_types = json_decode(json_encode(\App\Discount::DISCOUNT_TYPES));
        $selected_discount_types = $request->filter_discount_type;
        // return json_encode($Students);
        $total = 0 ;
        foreach ($Students as $student)
        {
            $sub_total = 0;

            if ($selected_discount_types == 0)
            {
                $total_discounts  = $student->total_scholar +
                                    $student->school_subsidy +
                                    $student->total_employee_scholar +
                                    $student->total_acad_scholar +
                                    $student->total_family_member +
                                    $student->total_nbi_alumni +
                                    $student->total_cash_discount +
                                    $student->total_cwoir_discount +
                                    $student->st_joseph_discount;
    
                $total += $total_discounts;
            }
            else if ($selected_discount_types == 1)
                $sub_total = $student->total_scholar;
            else if ($selected_discount_types == 2)
                $sub_total = $student->school_subsidy;
            else if ($selected_discount_types == 3)
                $sub_total = $student->total_employee_scholar;
            else if ($selected_discount_types == 4)
                $sub_total = $student->total_acad_scholar;
            else if ($selected_discount_types == 5)
                $sub_total = $student->total_family_member;
            else if ($selected_discount_types == 6)
                $sub_total = $student->total_nbi_alumni;
            else if ($selected_discount_types == 7)
                $sub_total = $student->total_cash_discount;
            else if ($selected_discount_types == 8)
                $sub_total = $student->total_cwoir_discount;
            else if ($selected_discount_types == 9)
                $sub_total = $student->st_joseph_discount;
            
            $total += $sub_total;
        }

        // return json_encode($total);
        $request_data = $request->filter_discount_type; 
        return view('admin.student_discounts_list.partials.data_list', compact('Students', 'discount_types', 'selected_discount_types', 'total', 'request_data', 'Students_sum'))->render();
    }
}

