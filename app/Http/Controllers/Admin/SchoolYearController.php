<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\SchoolYear;
use App\StudentSchoolYearTag;
use App\Grade;
use App\Section;
use App\Discount;
use App\StudentDiscountList;
use App\AdditionalFeePayment;

class SchoolYearController extends Controller
{
    public function index ()
    {
        $SchoolYear = SchoolYear::paginate(10);
        return view('admin.manage_school_years.index', compact('SchoolYear'));
    }

    public function list_data (Request $request)
    {
        $pages = 10000;
        if ($request->show_count)
        {
            $pages = $request->show_count;
        }
        $SchoolYear = SchoolYear::where('school_year', 'like', '%'.$request->search.'%')->paginate($pages);
        return view('admin.manage_school_years.partials.data_list', compact('SchoolYear'))->render();
    }

    public function modal_school_year (Request $request)
    {
        $SchoolYear = NULL;
        if ($request->id)
        {
            $id = $request->id;
            $SchoolYear = SchoolYear::where('id', $id)->first();
        }
        return view('admin.manage_school_years.partials.form_modal', compact('SchoolYear'))->render();
    }

    public function save_data (Request $request)
    {
        $rules = [
            'school_year' => 'required'
        ];
        $Validator = \Validator::make($request->all(), $rules);

        if ($Validator->fails())
        {
            return response()->json(['code' => 1, 'general_message' => 'Please fill the required field.', 'messages' => $Validator->getMessageBag()]);
        }

        if ($request->id)
        {

            $SchoolYear = SchoolYear::where('school_year', 'like', '%'.$request->school_year.'%')->where('id', '!=', $request->id)->first();
            
            if ($SchoolYear)
            {
                return response()->json(['code' => 2, 'general_message' => 'School Year already existing.', 'messages' => []]);
            }

            $SchoolYear = SchoolYear::where('id', $request->id)->first();
            $SchoolYear->school_year = $request->school_year;
            $SchoolYear->save();
            return response()->json(['code' => 0, 'general_message' => 'School year successfully saved.', 'messages' => []]);
        }

        $SchoolYear = SchoolYear::where('school_year', 'like', '%'.$request->school_year.'%')->first();
        
        if ($SchoolYear)
        {
            return response()->json(['code' => 2, 'general_message' => 'School Year already existing.', 'messages' => []]);
        }
        $SchoolYear = new SchoolYear();
        $SchoolYear->school_year = $request->school_year;
        $SchoolYear->save();
        return response()->json(['code' => 0, 'general_message' => 'School year successfully saved.', 'messages' => []]);

    }

    public function student_school_year_tagged ($sy_id)
    {
        $Students  = StudentSchoolYearTag::with(['student_info', 'grade', 'section'])
        ->where('school_year_id', $sy_id)->paginate(10);
        $Grade = Grade::all();
        $Section = Section::where('grade_id', 1)->get();
        $SchoolYear = SchoolYear::where('id', $sy_id)->first();
        return view('admin.school_year_tagged_students.index', compact('Students', 'Grade', 'Section', 'sy_id', 'SchoolYear'))->render();
    }

    public function student_school_year_tagged_list_data (Request $request)
    {
        $pages = 10;
        if ($request->show_count == '')
        {
            $pages = 100000;
        }
        else
        {
            $pages = $request->show_count;
        }
        $Students  = StudentSchoolYearTag::with(['student_info', 'grade', 'section'])
        ->where(function ($query) use ($request) {
            if ($request->filter_grade)
            {
                $query->where('grade_id', $request->filter_grade);
            }

            if ($request->filter_section)
            {
                $query->where('section_id', $request->filter_section);
            }
        })
        ->where('school_year_id', $request->sy_id)
        ->whereHas('student_info', function ($query) use ($request) {
            $query->whereRaw("concat(first_name, ' ', middle_name , ' ', last_name) like '%". $request->search_filter ."%' ");
        })
        ->paginate($pages);
        // return json_encode($Students);
        return view('admin.school_year_tagged_students.partials.data_list', compact('Students'))->render();
    }
    
    public function form_modal (Request $request)
    {
        $Student = NULL;
        $Student = StudentSchoolYearTag::with([
            'student_info',
            'grade',
            'section', 
            'discount_list'  => function ($query) use ($request) {
                $query->where('school_year_id', $request->sy_id);
            },
        ])
        ->where('id', $request->id)
        ->where('school_year_id', $request->sy_id)
        ->first();
        // $Student = StudentSchoolYearTag::where('id', $request->id)->where('school_year_id', $request->sy_id)->first();
        
        $AdditionalFeePayment = AdditionalFeePayment::where('student_id', $Student->student_id)->where('school_year_id', $request->sy_id)->first();
        $Grade = Grade::all();
        $Section = Section::where('grade_id', 1)->get();
        $Discount = Discount::where('status', 1)->get();
        // return json_encode(['Student' => $Student, 'Grade' => $Grade, 'Section' => $Section, 'Discount' => $Discount]);
        return view('admin.school_year_tagged_students.partials.form_modal', ['Student' => $Student, 'Grade' => $Grade, 'Section' => $Section, 'Discount' => $Discount, 'sy_id' => $request->sy_id, 'AdditionalFeePayment' => $AdditionalFeePayment])->render();
    }

    public function save_data_student (Request $request)
    {
        
        $rules      = [
                'grade'             => 'required',
                'section'           => 'required',
                
        ];
        
        $Validator  = \Validator::make($request->all(), $rules);

        if ($Validator->fails())
        {
            return response()->json(['code' => 1, 'general_message' => 'Please fill the required fields.', 'messages' => $Validator->getMessageBag()]);
        }
        $Student = StudentSchoolYearTag::where('id', $request->id)->where('school_year_id', $request->sy_id)->first();
        $Student->grade_id          = $request->grade;
        $Student->section_id        = $request->section;
        $Student->status            = 1;
        $Student->save();
        
        $StudentDiscountList = StudentDiscountList::where('student_id', $Student->student_id)->first();
        $StudentDiscountList->scholar = ($request->scholar              ? $request->scholar             / 100   : '0.00');
        $StudentDiscountList->school_subsidy = ($request->school_subsidy       ? $request->school_subsidy              : '0.00');
        $StudentDiscountList->employee_scholar = ($request->employee_scholar     ? $request->employee_scholar    / 100   : '0.00');
        $StudentDiscountList->gov_subsidy = ($request->gov_subsidy          ? $request->gov_subsidy                 : '0.00');
        $StudentDiscountList->acad_scholar = ($request->acad_scholar         ? $request->acad_scholar        / 100   : '0.00');
        $StudentDiscountList->family_member = ($request->family_member        ? $request->family_member       / 100   : '0.00');
        $StudentDiscountList->nbi_alumni = ($request->nbi_alumni           ? $request->nbi_alumni          / 100   : '0.00');
        $StudentDiscountList->cash_discount = ($request->cash_discount        ? $request->cash_discount       / 100   : '0.00');
        $StudentDiscountList->cwoir_discount = ($request->cwoir_discount       ? $request->cwoir_discount      / 100   : '0.00');
        $StudentDiscountList->st_joseph_discount = ($request->st_jospeh_discount   ? $request->st_jospeh_discount          : '0.00');
        $StudentDiscountList->other_discount = ($request->other_discount   ? $request->other_discount          : '0.00');
        $StudentDiscountList->student_id = $Student->student_id;
        $StudentDiscountList->school_year_id = $request->sy_id;
        $StudentDiscountList->save();

        if ($Student->additional_fee_payment == NULL)
        {
            $AdditionalFeePayment = new \App\AdditionalFeePayment();
            $AdditionalFeePayment->student_id = $Student->student_id;
            $AdditionalFeePayment->school_year_id = $request->sy_id;
            $AdditionalFeePayment->save();
        }

        $AdditionalFeePayment = AdditionalFeePayment::where('school_year_id', $request->sy_id)->where('student_id', $Student->student_id)->first();
        $AdditionalFeePayment->book_remarks = $request->book_remarks;
        $AdditionalFeePayment->save();
        return response()->json(['code' => 0, 'general_message' => 'Student information successfully updated.', 'messages' => []]);
    }

    public function deactivate_student(Request $request) 
    {
        $StudentSchoolYearTag = StudentSchoolYearTag::where('id', $request->id)->first();

        if (!$StudentSchoolYearTag)
        {
            return response()->json(['code' => 2, 'general_message' => 'Invalid selection.', 'messages' => []]);
        }
        $StudentSchoolYearTag->status = 0;
        $StudentSchoolYearTag->save();
        return response()->json(['code' => 0, 'general_message' => 'Student successfully deactivated.', 'messages' => []]);

    }
}
