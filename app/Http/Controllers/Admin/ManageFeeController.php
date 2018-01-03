<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use DB;

use App\Grade;
use App\TuitionFee;
use App\AdditionalFee;

class ManageFeeController extends Controller
{
    public function index ($sy_id = 1)
    {
        $Grade_Tuition = Grade::with(['tuition_fee' => function ($query) use($sy_id) {
                        $query->where('status', 1);
                        $query->where('school_year_id', $sy_id);
                    }, 'additional_fee' => function ($query)  use($sy_id) {
                        $query->where('status', 1);
                        $query->where('school_year_id', $sy_id);    
                    }])
                    ->get();
        $Grade = Grade::all();
        // return json_encode($Grade_Tuition);
        return view('admin.manage_fees.index', ['Grade_Tuition' => $Grade_Tuition, 'Grade' => $Grade, 'sy_id' => $sy_id]);
    }

    public function list (Request $request)
    {
        $sy_id = $request->sy_id;
        $Grade_Tuition = Grade::with(['tuition_fee' => function ($query) use($sy_id) {
                                    $query->where('status', 1);
                                    $query->where('school_year_id', $sy_id);   
                                }, 'additional_fee' => function ($query) use($sy_id) {
                                    $query->where('status', 1);
                                    $query->where('school_year_id', $sy_id);   
                                }])
                                ->where(function ($query) use ($request) {
                                    if ($request->filter_grade)
                                    {
                                        $query->where('id', $request->filter_grade);
                                    }
                                })
                                ->get();
                                
        return view('admin.manage_fees.partials.data_list', ['Grade_Tuition' => $Grade_Tuition]);
    }

    public function form_modal (Request $request)
    {
        $TuitionFee = NULL;
        $AdditionalFee = NULL;
        if ($request->id)
        {
            $TuitionFee = TuitionFee::with(['grade'])->where('grade_id', $request->id)->where('school_year_id', $request->sy_id)->first();
            $AdditionalFee = AdditionalFee::where('grade_id', $request->id)->where('school_year_id', $request->sy_id)->get();
        }
        
        $Grade = Grade::where('id', $request->id)->first();
        return view('admin.manage_fees.partials.form_modal', ['TuitionFee' => $TuitionFee, 'AdditionalFee' => $AdditionalFee, 'grade_id' => $request->id, 'Grade' => $Grade, 'sy_id' => $request->sy_id])->render();
    }

    public function save_data (Request $request)
    {
         $rules      = [
                'tuition_fee'       =>'required',
                'misc_fee'          =>'required',
                'book'              => 'required',
                'speech_lab'        => 'required',
                'pe_uniform'        => 'required',
                'school_uniform'    => 'required',
                'upon_enrollment'   => 'required',
                
        ];
        
        $Validator  = Validator::make($request->all(), $rules);

        if ($Validator->fails())
        {
            return response()->json(['code' => 1, 'general_message' => 'Please fill the required fields.', 'messages' => $Validator->getMessageBag()]);
        }
        

        DB::beginTransaction();

        if ($request->id)
        {
            $TuitionFee = \App\TuitionFee::where('id', $request->id)
                                            ->where('status', 1)
                                            ->first();
            $TuitionFee->tuition_fee    = $request->tuition_fee;
            $TuitionFee->misc_fee       = $request->misc_fee;
            $TuitionFee->upon_enrollment= $request->upon_enrollment;
            $TuitionFee->save();
        }
        else
        {
            $TuitionFee = new \App\TuitionFee();
            $TuitionFee->tuition_fee    = $request->tuition_fee;
            $TuitionFee->misc_fee       = $request->misc_fee;
            $TuitionFee->upon_enrollment= $request->upon_enrollment;
            $TuitionFee->school_year_id = $request->sy_id;
            $TuitionFee->grade_id       = $request->grade_id;
            $TuitionFee->school_year    = '';
            $TuitionFee->save();
        }

        if ($request->book_id)
        {
            $AdditionalFee = \App\AdditionalFee::where('id', $request->book_id)
                                                ->where('status', 1)
                                                ->first();
            $AdditionalFee->additional_amount   = $request->book;
            $AdditionalFee->save();
        }
        else
        {
            $AdditionalFee = new \App\AdditionalFee();
            $AdditionalFee->additional_amount = $request->book;
            $AdditionalFee->school_year_id    = $request->sy_id;
            $AdditionalFee->grade_id          = $request->grade_id;
            $AdditionalFee->additional_title  = 'Books (Annually)';
            $AdditionalFee->school_year       = '';
            $AdditionalFee->save();
        }

        if ($request->speech_lab_id)
        {
            $AdditionalFee = \App\AdditionalFee::where('id', $request->speech_lab_id)
                                                ->where('status', 1)
                                                ->first();
            $AdditionalFee->additional_amount = $request->speech_lab;
            $AdditionalFee->save();
        }
        else
        {
            $AdditionalFee = new \App\AdditionalFee();
            $AdditionalFee->additional_amount = $request->speech_lab;
            $AdditionalFee->school_year_id    = $request->sy_id;
            $AdditionalFee->grade_id          = $request->grade_id;
            $AdditionalFee->additional_title  = 'Speech Lab (Annually)';
            $AdditionalFee->school_year       = '';
            $AdditionalFee->save();
        }
        
        if ($request->pe_uniform_id)
        {
            $AdditionalFee = \App\AdditionalFee::where('id', $request->pe_uniform_id)
                                                ->where('status', 1)
                                                ->first();
            $AdditionalFee->additional_amount = $request->pe_uniform;
            $AdditionalFee->save();
        }
        else
        {
            $AdditionalFee = new \App\AdditionalFee();
            $AdditionalFee->additional_amount = $request->pe_uniform;
            $AdditionalFee->school_year_id    = $request->sy_id;
            $AdditionalFee->grade_id          = $request->grade_id;
            $AdditionalFee->additional_title  = 'P.E Uniform/Set';
            $AdditionalFee->school_year       = '';
            $AdditionalFee->save();
        }

        if ($request->school_uniform_id)
        {
            $AdditionalFee = \App\AdditionalFee::where('id', $request->school_uniform_id)
                                                ->where('status', 1)
                                                ->first();
            $AdditionalFee->additional_amount = $request->school_uniform;
            $AdditionalFee->save();
        }
        else
        {
            $AdditionalFee = new \App\AdditionalFee();
            $AdditionalFee->additional_amount = $request->school_uniform;
            $AdditionalFee->school_year_id    = $request->sy_id;
            $AdditionalFee->grade_id          = $request->grade_id;
            $AdditionalFee->additional_title  = 'School Uniform/Set';
            $AdditionalFee->school_year       = '';
            $AdditionalFee->save();
        }

        DB::commit();
        return response()->json(['code' => 0, 'general_message' => 'Student information successfully saved.', 'messages' => []]);
        

    }

}
