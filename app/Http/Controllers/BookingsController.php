<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Port_name;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class BookingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rates = Booking::all();

        //return view('ra.index', compact('rates'));
        return response()->json($rates);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $data = Booking::find($id);
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function user(Request $request){
        $userID = $this->customerIdFromEmail($request->email);
        // echo 'hi'; exit;
        //dd($userID);

        // $data = DB::table('bookings as bo')->
        // leftJoin('port_name as pol_table', 'pol_table.ID','=','bookings.POL')->
        // leftJoin('port_name as pod_table', 'pod_table.ID','=','bookings.POD')
        // ->select('bo.*,', 'pol_table.port_name as POL')
        // ->get();
        $data = Booking::with('pol')->with('pod')->with('cs_status')->where('CustomerName', $userID)->get();
        // $formatted_data = array()
        // dd($data);
        $i=0;
        // foreach ($data as $row) {
        //     // echo $k;
        //     // $data['']->push(array('polname' =>$row->pol->port_name));
        //     $data[$i]['POL_name'] = $row->pol->port_name;
        //     $data[$i]['POD_name'] = $row->pod->port_name;
        //     // $i++;
        //     // echo 'POL: '.$row->pol->port_name.' ----> POD:'.$row->pod->port_name.' <br>';
        // }
        //dd($data);

        // $data = Booking::with('pol')->where('CustomerName', $userID)->get();
        // dd($data->pol->port_name);
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
    public function create()
    {
        //
    }

    public function customerIdFromEmail($email){
        $codes = Customer::where('email','LIKE','%'.$email.'%')->get();
        if(count($codes)!==0){
            return $codes[0]['ID'];
        }
        else {
            return 'False';
        }
        
    }
    public function store(Request $request)
    {
        // $request->validate([
        //     'title' => 'required|string|max:255',
        //     'description' => 'required|string|max:255',
        // ]);
        
        $req = '{"ID":500,"sl_name":"COSCO","from_port":"Jawaharlal Nehru","to_port":"COLOMBO","_20gp":"CASE BY CASE","Margin":0,"FAF":"109","seal_charge":"5","ECC":"","service_mode":"","direct_via":"","via_port":"","transit_time":"","expiry_date":"2022-09-15 12:36:57","sl_logo":"http://launchindia.org/transpost/logos/cosco_logo.png","remarks":"","terms":"","base_rate":"CASE BY CASE","total":"CASE BY CASE","cargoSize":"20gp","email":"test123@test.com"}';
        $jreq = json_decode($req);
        dd($jreq);
        // $todo = Booking::create([
        //     'title' => $request->title,
        //     'description' => $request->description,
        // ]);

        // return response()->json([
        //     'status' => 'success',
        //     'message' => 'Todo created successfully',
        //     'todo' => $todo,
        // ]);

    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(Request $request)
    // {
    //     //
    // }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    // public function show(Booking $booking)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Booking $booking)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Booking  $booking
     * @return \Illuminate\Http\Response
     */
    public function destroy(Booking $booking)
    {
        //
    }
}
