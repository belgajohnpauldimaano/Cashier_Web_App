<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use Carbon;
use DB;

use App\Discount;

class ManageDiscountsController extends Controller
{
    public function index ()
    {
        $Discount = Discount::where('status', 1)
                                ->paginate(10);


        return view('admin.manage_discounts.index', ['Discount' => $Discount]);
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
        
        $Discount = Discount::where(function ($query) use ($request){
                                    $query->where('discount_title', 'like', '%'. $request->search_filter .'%');
                                })
                                ->where('status', 1)
                                ->paginate($pages);

        return view('admin.manage_discounts.partials.data_list', ['Discount' => $Discount]);
    }

    public function form_modal (Request $request)
    {
        $Discount = NULL;
        if ($request->id)
        {
            $Discount = Discount::where('id', $request->id)->first();
            // return json_encode($Student);
        }
        
        return view('admin.manage_discounts.partials.form_modal', ['Discount' => $Discount])->render();
    }

    public function save_data (Request $request)
    {
         $rules      = [
                'discount_title'        => 'required',
                'discount_amount'       => 'required',
                
        ]; 
        
        $Validator  = Validator::make($request->all(), $rules);

        if ($Validator->fails())
        {
            return response()->json(['code' => 1, 'general_message' => 'Please fill the required fields.', 'messages' => $Validator->getMessageBag()]);
        }

        if ($request->id)
        {
            $Discount = Discount::where('id', $request->id)->first();


            $Discount->discount_title        = $request->discount_title;
            $Discount->discount_amount       = $request->discount_amount;
            $Discount->save();

            return response()->json(['code' => 0, 'general_message' => 'Student information successfully saved.', 'messages' => []]);
        }


        $Discount = new Discount();

        $Discount->discount_title        = $request->discount_title;
        $Discount->discount_amount       = $request->discount_amount;
        $Discount->save();

        
        return response()->json(['code' => 0, 'general_message' => 'Student information successfully saved.', 'messages' => []]);
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
}
