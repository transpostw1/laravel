<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class AuthController extends Controller
{

    public function __construct()
     {
         $this->middleware('auth:api', ['except' => ['login','register']]);
     }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        $email = $request->email;
        $password = $request->password;
        $credentials = $request->only('email', 'password');
        $user = User::where('email', '=', $email)->first();
        $customer = DB::table('customer')->select('name','email','phone','contact_person','gst_certificate','pan_card')->where('email', '=', $email)->first();
        if($customer->gst_certificate!=NULL && $customer->pan_card!=NULL){
            $message = 'Customer Created Successfully, KYC Submitted.';
            $kyc_status = True;
        }
        else{
            $message = 'Account Created, Please complete your KYC to Access All features.';
            $kyc_status = False;
        }
        if (Hash::check($password, $user->password))
        {
    return response()->json([
                'status' => 'success',
                'customer' => $customer,
                'message' => $message,
                'KYC' => $kyc_status,
                'token' => $user->remember_token,
            ]);
}
else{
    return response()->json([
        'status' => 'error',
        'message' => 'Unauthorized',
    ], 401);
}


    }

    public function register(Request $request){
        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'required|string',
             'email' => 'required|string|email|max:255|unique:users',
             'phone' => 'required|string',
             'companyName' => 'required|string|max:255',
             'customer_type' => 'required|string|max:255',
         ]);
         if (DB::table('tb_users')->where('email', $request->email)->exists()) {
            $error = 'Email Id already exist';
            return response()->json($error);
         }
         else{

         $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            //'remember_token' => Str::random(60),
        ]);
        $customer = Customer::create([
            'name' => $request->companyName,
            'email' => $request->email,
            'phone' => $request->phone,
            'customer_type' => $request->customer_type,
            'contact_person' => $request->username,
            'gst_certificate' => $request->gst_certificate,
            'pan_card' => $request->pan_card,

        ]);
        $token = Auth::login($user);
        $user->remember_token = $token;
        //$token = $user['remember_token'];
        $user->save();
        $customer->save();
        if($request->gst_certificate && $request->pan_card){
            $message = 'Customer Created Successfully, KYC Submitted.';
            $kyc_status = True;
        }
        else{
            $message = 'Account Created, Please complete your KYC to Access All features.';
            $kyc_status = False;
        }
        return response()->json([
            'token' => $token,
            'status' => 'success',
            'message' => $message,
            'customer' => $customer,
            'KYC' => $kyc_status
        ]);
    }
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

}
