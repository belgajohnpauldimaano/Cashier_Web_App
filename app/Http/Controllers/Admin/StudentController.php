<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use Carbon;
use DB;
use Auth;

use App\Student;
use App\Grade;
use App\Section;
use App\StudentTuitionFee;
use App\Discount;
use App\StudentDiscount;
use App\StudentDiscountList;
class StudentController extends Controller
{
    public function index ()
    {
        $Students = Student::with(['grade', 'section'])
                            ->where('status', 1)
                            ->paginate(10);
        $Grade = Grade::all();
        $Section = Section::where('grade_id', 1)->get();

        return view('admin.manage_student.index', ['Students' => $Students, 'Grade' => $Grade, 'Section' => $Section]);
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
        $Students = Student::where(function ($query) use ($request){
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
                            // ->orWhereHas('grade', function ($query) use ($request) {
                            //     //$query->where('grade', $request->grade);
                            // })
                            ->paginate($pages);
        
        return view('admin.manage_student.partials.data_list', ['Students' => $Students]);
    }

    public function form_modal (Request $request)
    {
        $Student = NULL;
        if ($request->id)
        {
            $Student = Student::with(['grade', 'section', 'discount_list'])->where('id', $request->id)->first();
            // return json_encode($Student);
        }
        $Grade = Grade::all();
        $Section = Section::where('grade_id', 1)->get();
        $Discount = Discount::where('status', 1)->get();
        return view('admin.manage_student.partials.form_modal', ['Student' => $Student, 'Grade' => $Grade, 'Section' => $Section, 'Discount' => $Discount])->render();
    }

    public function save_data (Request $request)
    {

         $rules      = [
                'first_name'        => 'required',
                'middle_name'       => 'required',
                'last_name'         => 'required',
                'grade'             => 'required',
                'section'           => 'required',
                
        ];
        
        $Validator  = Validator::make($request->all(), $rules);

        if ($Validator->fails())
        {
            return response()->json(['code' => 1, 'general_message' => 'Please fill the required fields.', 'messages' => $Validator->getMessageBag()]);
        }

        if ($request->id)
        {
            $Student = Student::with('additional_fee_payment')->where('id', $request->id)->first();

            /**
             *  THIS CODE IS FOR CHANGES ON GRADE THAT COULD CHANGE ALSO TUITION FEE
             */
            /*if ($Student->grade_id != $request->grade)
            {
                $TuitionFee = \App\TuitionFee::where('grade_id', $request->grade)
                                                ->where('status', 1)
                                                ->first();

                if (!$TuitionFee)
                {
                    DB::rollBack();
                    return response()->json(['code' => 2, 'general_message' => 'No available tuition fee for selected grade.', 'messages' => []]);
                }
                
                $total_tuition  = $TuitionFee->tuition_fee + $TuitionFee->misc_fee;
                $dp_amount      = $TuitionFee->misc_fee + 2000;

                $StudentTuitionFee = StudentTuitionFee::where('student_id', $Student->id)
                                                        ->where('status', 1)
                                                        ->where('school_year', $this->formulate_sy())
                                                        ->first();
                $total_payment = $StudentTuitionFee->total_payment;

                $dp_paid = false;

                if ($total_payment > $dp_amount)
                {
                    $total_payment = $total_payment - $dp_amount;
                    $dp_paid = true;
                }

                while($total_payment > 0)
                {

                }

            }*/

            $Student->first_name        = $request->first_name;
            $Student->middle_name       = $request->middle_name;
            $Student->last_name         = $request->last_name;
            $Student->grade_id          = $request->grade;
            $Student->section_id        = $request->section;
            $Student->save();
            
            $StudentDiscountList = StudentDiscountList::where('student_id', $request->id)->first();
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
            $StudentDiscountList->student_id = $Student->id;
            $StudentDiscountList->save();

            if ($Student->additional_fee_payment == NULL)
            {
                $AdditionalFeePayment = new \App\AdditionalFeePayment();
                $AdditionalFeePayment->student_id = $Student->id;
                $AdditionalFeePayment->save();
            }

            return response()->json(['code' => 0, 'general_message' => 'Student information successfully saved.', 'messages' => []]);
        }

        try 
        {
            DB::beginTransaction();
            $Student = new Student();
            $Student->student_number    = '';
            $Student->first_name        = $request->first_name;
            $Student->middle_name       = $request->middle_name;
            $Student->last_name         = $request->last_name;
            $Student->grade_id          = $request->grade;
            $Student->section_id        = $request->section;
            $Student->save();

            
        
            $StudentDiscountList = new StudentDiscountList();
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
            $StudentDiscountList->student_id = $Student->id;
            $StudentDiscountList->save();

            
            $StudentTuitionFee = new StudentTuitionFee();
            $StudentTuitionFee->student_id  = $Student->id;
            $StudentTuitionFee->school_year = $this->formulate_sy();
            $StudentTuitionFee->save();

            $AdditionalFeePayment = new \App\AdditionalFeePayment();
            $AdditionalFeePayment->student_id = $Student->id;
            $AdditionalFeePayment->save();
            DB::commit();
            return response()->json(['code' => 0, 'general_message' => 'Student information successfully saved.', 'messages' => []]);
        }
        catch (Exception $e)
        {
            DB::rollBack();
            return response()->json(['code' => 1, 'general_message' => 'Error in saving student information.', 'messages' => []]);
        }

        // $TuitionFee = \App\TuitionFee::where('grade_id', $request->grade)
        //                                 ->where('status', 1)
        //                                 ->first();
        
        // $AdditionalFee = \App\AdditionalFee::selectRaw('sum(additional_amount) as additional_fee_total')
        //                                         ->where('grade_id', $request->grade)
        //                                         ->where('status', 1)
        //                                         ->first();
        // $add_fee = 0;
        // if ($AdditionalFee)
        // {
        //     $add_fee += $AdditionalFee->additional_fee_total;
        // }
        // if (!$TuitionFee)
        // {
        //     DB::rollBack();
        //     return response()->json(['code' => 2, 'general_message' => 'No available tuition fee for selected grade.', 'messages' => []]);
        // }
        
        // $discount_sum = 0;
        // foreach ($request->discounts as $key => $data)
        // {
        //     $Discount        = Discount::where('id', $key)->first();
        //     $discount_sum   += $Discount->discount_amount;

        //     $StudentDiscount                = new StudentDiscount();
        //     $StudentDiscount->student_id    = $Student->id;
        //     $StudentDiscount->discount_id   = $Discount->discount_amount;
        //     $StudentDiscount->created_by    = Auth::user()->id;
        //     $StudentDiscount->school_year   = '';
        //     $StudentDiscount->save();
        // }

        
    }

    public function delete (Request $request)
    {
        if (!$request->id)
        {
            return response()->json(['code' => 2, 'general_message' => 'Invalid selection of data.', 'messages' => []]);
        }

        $Student = Student::where('id', $request->id)->first();
        $Student->status = 0;
        $Student->save();
        return response()->json(['code' => 0, 'general_message' => 'Student information successfully deleted.', 'messages' => []]);
    }

    public function formulate_sy ()
    {
        $current_mon = Carbon\Carbon::now('Asia/Manila')->format('m');
        $sy = '';
        if ($current_mon >= 2)
        {
            $sy = Carbon\Carbon::now('Asia/Manila')->format('Y') . '-' . (Carbon\Carbon::now('Asia/Manila')->format('Y') + 1);
        }

        return $sy;
    }

    public function test_data ()
    {
        $AdditionalFees = \App\AdditionalFee::selectRaw('sum(additional_amount) as additional_fee_total')
                                                ->where('grade_id', 1)
                                                ->where('status', 1)
                                                ->first();
        echo $AdditionalFees->additional_fee_total;
        return json_encode($AdditionalFees);
    }
}
