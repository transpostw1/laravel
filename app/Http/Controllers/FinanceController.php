<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Freight_finance;

class FinanceController extends Controller
{
    public function store(Request $request)
    {
        $isShipper= $request->isShipper;
    $isForwarder= $request->isForwarder;

    if($isShipper==TRUE){
        $customer_type = 'Shipper';
        $finance = Freight_finance::create([
            'name'=> $request->name,
            'commodity_name'=> $request->commodity_name,
            'company_name'=> $request->company_name,
            'iec_code'=> $request->iec_code,
            'annual_turnover'=> $request->annual_turnover,
            'product'=> $request->product,
            'yoc'=> $request->yoc,
            'customer_type'=> $customer_type
    ]);
}
else{
    $customer_type = 'Forwarder';
        $finance = Freight_finance::create([
            'name'=> $request->name,
            'commodity_name'=> $request->commodity_name,
            'company_name'=> $request->company_name,
            'gst'=> $request->gst,
            'pan'=> $request->pan,
            'annual_turnover'=> $request->annual_turnover,
            'product'=> $request->product,
            'yoc'=> $request->yoc,
            'customer_type'=> $customer_type
    ]);
}
    $finance->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Created successfully',
            'Freight_finance' => $finance,
        ]);

    }
}
