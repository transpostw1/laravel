<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:api');
    // }

    public function index()
    {
        $todos = Customer::all();
        return response()->json([
            'status' => 'success',
            'todos' => $todos,
        ]);
    }

    public function store(Request $request)
    {
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|string|max:255',
        // ]);

        $customer = Customer::create([
            'name' => $request->fullName,
            'email' => $request->email,
            'phone' => $request->phoneNumber,
            'companyName'=>$request->companyName,
            'gst_certificate'=> $request->gst,
            'pan_card'=> $request->pan,
            'businessType'=> $request->businessType

        ]);

        if($request->gst && $request->pan){
            $message = 'Customer Created Successfully, KYC Submitted.';
            $kyc_status = True;
        }
        else{
            $message = 'Account Created, Please complete your KYC to Access All features.';
            $kyc_status = False;
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'customer' => $customer,
            'KYC' => $kyc_status
        ]);
    }
    public function updatekyc(Request $request){
        $ID = $request->customerID;

        $message = 'Account Created, Please complete your KYC to Access All features.';


        $customer = Customer::find($ID);
        //dd($customer);
        $customer->gst_certificate = $request->gst;
        $customer->pan_card = $request->pan;
        $customer->save();
        $kyc_status = True;
        return response()->json([
            'status' => 'success',
            'message' => 'Kyc updated successfully',
            'KYC' => $kyc_status,
        ]);


    }
    public function show($id)
    {
        $todo = Customer::find($id);
        return response()->json([
            'status' => 'success',
            'todo' => $todo,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ]);

        $todo = Customer::find($id);
        $todo->title = $request->title;
        $todo->description = $request->description;
        $todo->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Todo updated successfully',
            'todo' => $todo,
        ]);
    }

    public function destroy($id)
    {
        $todo = Customer::find($id);
        $todo->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Todo deleted successfully',
            'todo' => $todo,
        ]);
    }
}
