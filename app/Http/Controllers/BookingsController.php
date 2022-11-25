<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Port_name;
use App\Models\Booking_stage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Mail;
use App\Mail\RequestNotify;


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
        $date = Carbon::now();
        $now = $date->format("Y-m-d");
        $commodity = json_decode($request->commodityDetails);
       //dd($this->customerIdFromEmail($request->email));

$bookingData = array(
    'CS_User' => $request->id,
    'DateOfBooking' => $now,
    'ContainerType' => $request->cargo_size,
    'TypeOfOnboarding' => 'Online',
    'ShippingLineName' => $request->sl_name,
    'POL' => $this->port_id_from_code($request->from_port),
    'POD' => $this->port_id_from_code($request->to_port),
    'BuyRate' => $request->description,
    'SellRate' => $request->total,
    'ContainerCount' => $commodity->containerCount,
    'commodity' => $commodity->commodityName,
    'weight' => $commodity->weight,
    'CustomerName'=> $this->customerIdFromEmail($request->email)

);

//dd($bookingData);
        $booking = Booking::create($bookingData);
        $mailstatus = $this->sendEmail($booking->ID);
        //dd($bkng);
        return response()->json([
            'status' => 'success',
            'message' => 'Booking created successfully',
            'Booking' => $booking,
            'MailStatus'=>$mailstatus

        ]);

    }

    public function timeline(Request $request){
        // dd();
        $stage = Booking_stage::with('status')->where('bookingID', '=', $request->bookingID)->get();
        return response()->json([
            'status' => 'success',
            'bookingID' => $request->bookingID,
            'data' => $stage,
            'html' => '<a href="google.com">Click to take some action</a>',
        ]);
    }

    public function sendEmail($bid)
    {
      //$user = auth()->user();
    $bkng = DB::table('bookings')->where('ID', $bid)->first();
	$cusID = $bkng->CustomerName;
	$customer = DB::table('customer')->where('ID', $cusID)->first();
	//dd($bkng);
	$booking['requestid'] = $bid;
	$booking['POL'] =  $this->port_name($bkng->POL);
	$booking['POD'] =  $this->port_name($bkng->POD);
    $booking['ContainerCount'] = $bkng->ContainerCount;
    $booking['commodity'] = $bkng->commodity;
    $booking['SellRate'] = $bkng->SellRate*$bkng->ContainerCount;

	$booking['user']['name'] = $customer->name;
	$booking['user']['email'] = $customer->email;


 		if (Mail::to($booking['user']['email'])->send(new RequestNotify($booking))) {
            return ['message'=>'mail sent','status'=>'success'];
			}else{
				return ['message'=>'mail not sent','status'=>'failure'];
			 }

    }

    public function port_name($portCode){
        $codes = Port_name::where('port_code','LIKE','%'.$portCode.'%')->get();
        if(count($codes)!==0){
            return $codes[0]['port_name'];
        }
        else {
            return 'False';
        }

    }
    public function port_code($portName){
        $codes = Port_name::where('port_name','LIKE','%'.$portName.'%')->get();
        if(count($codes)!==0){
            return $codes[0]['port_code'];
        }
        else {
            return 'False';
        }

    }

    public function port_id($portName){
        $codes = Port_name::where('port_name','LIKE','%'.$portName.'%')->get();
        if(count($codes)!==0){
            return $codes[0]['ID'];
        }
        else {
            return 'False';
        }

    }

    public function port_id_from_code($portCode){
        $codes = Port_name::where('port_code','LIKE','%'.$portCode.'%')->get();
        if(count($codes)!==0){
            return $codes[0]['ID'];
        }
        else {
            return 'False';
        }

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
