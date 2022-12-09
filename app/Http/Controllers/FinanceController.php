<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Freight_finance;

class FinanceController extends Controller
{
    public function store(Request $request)
    {
    $FinanceData = array(
    'name'=> $request->name,
    'commodity_name'=> $request->commodity_name,
    'company_name'=> $request->company_name,
    'gst'=> $request->gst,
    'pan'=> $request->pan,
    'iec_code'=> $request->iec_code,
    'annual_turnover'=> $request->annual_turnover,
    'product'=> $request->product,
    'yoc'=> $request->yoc

);

        $finance = Freight_finance::create($FinanceData);
        return response()->json([
            'status' => 'success',
            'message' => 'Created successfully',
            'Freight_finance' => $finance,
        ]);

    }
}
