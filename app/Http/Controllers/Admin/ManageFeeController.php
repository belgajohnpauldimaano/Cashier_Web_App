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
    public function index ()
    {

        $Grade_Tuition = Grade::with(['tuition_fee' => function ($query) {
                        $query->where('status', 1);
                    }, 'additional_fee' => function ($query) {
                        $query->where('status', 1);
                    }])
                    // ->orderBy('grade', 'ASC')
                    ->get();
        $Grade = Grade::all();
        return view('admin.manage_fees.index', ['Grade_Tuition' => $Grade_Tuition, 'Grade' => $Grade]);
    }

    public function list (Request $request)
    {
        $Grade_Tuition = Grade::with(['tuition_fee' => function ($query) {
                                    $query->where('status', 1);
                                }, 'additional_fee' => function ($query) {
                                    $query->where('status', 1);
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
            $TuitionFee = TuitionFee::with(['grade'])->where('grade_id', $request->id)->first();
            $AdditionalFee = AdditionalFee::where('grade_id', $request->id)->get();
            return view('admin.manage_fees.partials.form_modal', ['TuitionFee' => $TuitionFee, 'AdditionalFee' => $AdditionalFee])->render();
        }
        
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
                
        ];
        
        $Validator  = Validator::make($request->all(), $rules);

        if ($Validator->fails())
        {
            return response()->json(['code' => 1, 'general_message' => 'Please fill the required fields.', 'messages' => $Validator->getMessageBag()]);
        }
        

        DB::beginTransaction();

        $TuitionFee = \App\TuitionFee::where('id', $request->id)
                                        ->where('status', 1)
                                        ->first();
        $TuitionFee->tuition_fee    = $request->tuition_fee;
        $TuitionFee->misc_fee       = $request->misc_fee;
        $TuitionFee->save();

        $AdditionalFee = \App\AdditionalFee::where('id', $request->book_id)
                                            ->where('status', 1)
                                            ->first();
        $AdditionalFee->additional_amount = $request->book;
        $AdditionalFee->save();

        $AdditionalFee = \App\AdditionalFee::where('id', $request->speech_lab_id)
                                            ->where('status', 1)
                                            ->first();
        $AdditionalFee->additional_amount = $request->speech_lab;
        $AdditionalFee->save();

        $AdditionalFee = \App\AdditionalFee::where('id', $request->pe_uniform_id)
                                            ->where('status', 1)
                                            ->first();
        $AdditionalFee->additional_amount = $request->pe_uniform;
        $AdditionalFee->save();

        $AdditionalFee = \App\AdditionalFee::where('id', $request->school_uniform_id)
                                            ->where('status', 1)
                                            ->first();
        $AdditionalFee->additional_amount = $request->school_uniform;
        $AdditionalFee->save();

        DB::commit();
        return response()->json(['code' => 0, 'general_message' => 'Student information successfully saved.', 'messages' => []]);
        

    }

}
