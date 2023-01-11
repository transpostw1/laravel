<?php

namespace App\Http\Controllers;

use App\Models\Rates;
use App\Models\Surcharge;
use App\Models\Rate_surcharge;
use App\Models\Search_history;
use App\Models\Port_name;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use GuzzleHttp\Client;
use PDF;
use DateTime;
use Illuminate\Support\Facades\DB;
use kamermans\OAuth2\GrantType\ClientCredentials;
use kamermans\OAuth2\OAuth2Middleware;
use kamermans\OAuth2\GrantType\PasswordCredentials;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
class RatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $rates = Rates::all();

        //return view('ra.index', compact('rates'));
        return response()->json($rates);
    }

    public function select(Request $request){

        $endDate = Carbon::now()->addMonths(3);
        $startDate = Carbon::now();

        $rates = Rates::with('rate_surcharge:amount,currency', 'surcharge:Code,Name,Term')->where('from_port', 'like', '%'.$request->from_port.'%')
                ->where('to_port', $request->to_port)
                ->whereBetween('expiry_date', [$startDate, $endDate])
                ->orderBy("_20gp")
                ->get();

        //dd($rates);
              $surcharges = $rates->pluck( 'surcharge' );


               //dd($rates);
              //return response()->json($surcharges); exit;
                $cargo_type = $request->cargo_type;

                if($cargo_type==='40hc'){
                  $rates->makeHidden(['_20gp', '_40gp']);
                }
                else if($cargo_type==='40gp'){
                    $rates->makeHidden(['_20gp', '_40hc']);
                }
                else if($cargo_type==='20gp'){
                    $rates->makeHidden(['_40hc', '_40gp']);
                }
                else{
                   // $rates->makeHidden();
                }
                //return response()->json($rates);
                 //dd($rates);
                $from_port_code =  $this->port_code($request->from_port);
                $to_port_code =  $this->port_code($request->to_port);
                $csize = substr($cargo_type, -4, 2);
                $cargotype = 'DRY '.$csize;
                $equipmentSize = $csize;
                if($equipmentSize == 20){
                    $equipmentIsoCode ="22G1";
                    $equipmentONECntrTpSz ="D2";
                   }
                   else{
                    $equipmentIsoCode = "42G1";
                    $equipmentONECntrTpSz = "D4";
                   }
               // $cma_live_data = $this->cma_rates($from_port_code, $to_port_code);



               foreach($rates as $rate){
                $stringID = $rate['ID'];
                unset($rate['ID']);
                $rate['id'] = 'TRA'.$stringID;
                $rate['base_rate'] = $rate["_".$cargo_type];
                $rate['Margin'] = 0;
                $rate['online_rates'] = FALSE;
                $rate['vesselName'] = 'Will be updated on confirmation of booking';
                $rate['cargo_size'] = $cargo_type;
                $rateArr = array();
                $freight = array();
                $freightdb= array();
                $freightdb[0]['chargeCode'] = "OFT";
                $freightdb[0]['chargeName'] = "BASIC OCEAN FREIGHT";
                $freightdb[0]['chargeTarget'] = "FREIGHT";
                $freightdb[0]['totalAmountInUSD'] = $rate['base_rate'];
                $freightdb[0]['chargeCurrency'] = "USD";
                $freightdb[0]['chargeId'] = 0;
                $freightdb[0]['chargeType'] = 'CN';
                $origin = array();
                $destination = array();
                $departures = array();

                    if(isset($rate['surcharge'])){
                        $surcharge = $rate['surcharge'];
                        $surchargeRate = $rate['rate_surcharge'];
                        $sum =0;
                        for ($i=0; $i < count($rate['surcharge']); $i++) {

                            if( $surcharge[$i]->Term == 'Freight'){
                                $freight[$i]['chargeCode'] = $surcharge[$i]->Code;
                                $freight[$i]['chargeName'] = $surcharge[$i]->Name;
                                $freight[$i]['chargeTarget'] = $surcharge[$i]->Term;
                                $freight[$i]['totalAmountInUSD'] = $surchargeRate[$i]->amount;
                                $sum += $surchargeRate[$i]->amount;
                                $freight[$i]['chargeCurrency'] = $surchargeRate[$i]->currency;
                            }
                            elseif( $surcharge[$i]->Term == "Origin"){
                               $origin[0]['chargeCode'] = $surcharge[$i]->Code;
                                $origin[0]['chargeName'] = $surcharge[$i]->Name;
                                $origin[0]['chargeTarget'] = $surcharge[$i]->Term;
                                $origin[0]['totalAmountInUSD'] = $surchargeRate[$i]->amount;
                                $sum += $surchargeRate[$i]->amount;
                                $origin[0]['chargeCurrency'] = $surchargeRate[$i]->currency;
                            }
                            elseif( $surcharge[$i]->Term == "Destination"){
                                $destination[0]['chargeCode'] = $surcharge[$i]->Code;
                                $destination[0]['chargeName'] = $surcharge[$i]->Name;
                                $destination[0]['chargeTarget'] = $surcharge[$i]->Term;
                                $destination[0]['totalAmountInUSD'] = $surchargeRate[$i]->amount;
                                $sum += $surchargeRate[$i]->amount;
                                $destination[0]['chargeCurrency'] = $surchargeRate[$i]->currency;
                            }
                            else{
                             $rateArr[$i]['chargeCode'] = $surcharge[$i]->Code;
                             $rateArr[$i]['chargeName'] = $surcharge[$i]->Name;
                             $rateArr[$i]['chargeTarget'] = $surcharge[$i]->Term;
                             $rateArr[$i]['totalAmountInUSD'] = $surchargeRate[$i]->amount;
                             $sum += $surchargeRate[$i]->amount;
                             $rateArr[$i]['chargeCurrency'] = $surchargeRate[$i]->currency;
                            }
                        }
                        unset($rate['surcharge']);
                        unset($rate['rate_surcharge']);
                        $rate['additionalCosts'] = $rateArr;
                        $rate['freightCharges'] = array_merge($freightdb,$freight);
                        $rate['originCharges'] = $origin;
                        $rate['destinationCharges'] = $destination;
                        $rate['vgmCutoff'] = '';
                        $rate['docCutoff'] = '';
                        $rate['cyCutoff'] = '';
                        $rate['departures'] = $departures;

                    }
                    else{
                        $rate['surcharge'] = NULL;
                    }
                    $rate['totalPrice'] = $rate["_".$cargo_type] + $sum;

   }

   /// adding online rates
   $getonelinerates = $this->oneline_rates($from_port_code, $to_port_code,$cargotype,$equipmentSize,$equipmentIsoCode,$equipmentONECntrTpSz);
             //dd(count($getonelinerates->data));
            $i=0;
                foreach($getonelinerates->data as $r){
                   //+ dd($r->freightInfos[0]->originCharges);

        // "sl_name": "HAPAG",
        // "from_port": "INNSA",
        // "to_port": "DEHAM",
        // "_20gp": 1400,
        // "Margin": 0,
        // "FAF": "",
        // "seal_charge": "",
        // "ECC": "",
        // "service_mode": "",
        // "direct_via": "",
        // "via_port": "",
        // "transit_time": "",
        // "expiry_date": "2022-12-31 00:00:00",
        // "sl_logo": "https://launchindia.org/transpost/logos/hepag.png",
        // "remarks": "",
        // "terms": "",
        // "commodity": "",
        // "id": "TRA3390",
                // "sl_logo": "https://backend.transpost.co/logos/hepag.png",


        $onelinerates['sl_name'] = 'ONE LIVE';
        $onelinerates['from_port'] = $from_port_code;
        $onelinerates['to_port'] = $to_port_code;
        $onelinerates['_20gp'] = $equipmentSize;
        // $onelinerates['Margin'] = 0;
        $onelinerates['online_rates'] = TRUE;
        $onelinerates['vesselName'] = $r->freightInfos[0]->departures[0]->transportName;
        // $onelinerates['FAF'] = "";
        // $onelinerates['seal_charge'] = "";
        // $onelinerates['ECC'] = "";
        $onelinerates['service_mode'] = $r->freightInfos[0]->serviceName;;
        $onelinerates['direct_via']  = "";
        $onelinerates['via_port'] = "";
        $date1 = new DateTime($r->departureDateEstimated);
        $date2 = new DateTime($r->arrivalDateEstimated);
        $transit_time=date_diff($date1,$date2);
        $onelinerates['transit_time'] = $transit_time->format("%a days");
        // dd($transit_time->format("%a days"));
        $onelinerates['expiry_date'] = $r->departureDateEstimated;
        $onelinerates['sl_logo'] = "https://backend.transpost.co/logos/ONE_live_logo.png";
        $onelinerates['remarks'] = "";
        $onelinerates['terms'] = "";
        $onelinerates['id'] = 'ONE'.rand(3,100);
        $onelinerates['base_rate'] = $r->freightInfos[0]->freightCharges[0]->totalAmountInUSD;
        $onelinerates['cargo_size'] = '_'.$csize.'gp';
        //$freightcharges = $r->freightInfos[0];
        //dd($freightcharges);
        //$origincharges = $r->freightInfos[0]->originCharges;
        //$destinationcharges = $r->freightInfos[0]->destinationCharges;
        //dd($r->freightInfos[0]->originCharges);
        //$originCharges = $r->freightInfos[0]->originCharges;
        //for($i=0;$i<count($originCharges);$i++){
           // $abc = $originCharges[$i];
           //$abc->id = $i;
           //$originCharges[$i] = $abc;
       // }
       // dd($originCharges);


        if(empty($r->freightInfos[0]->freightCharges)){
            $freightcharges = array();
        }
        else{
            $freightcharges = $r->freightInfos[0]->freightCharges;
            for($i=0;$i<count($freightcharges);$i++){
                $abc = $freightcharges[$i];
               $abc->chargeId = $i;
               $freightcharges[$i] = $abc;
            }
        }
        if(empty($r->freightInfos[0]->originCharges)){
            $origincharges = array();
        }
        else{
            $origincharges = $r->freightInfos[0]->originCharges;
            for($i=0;$i<count($origincharges);$i++){
                $abc = $origincharges[$i];
               $abc->chargeId = $i;
               $origincharges[$i] = $abc;
            }
        }
        if(empty($r->freightInfos[0]->destinationCharges)){
            $destinationcharges = array();
        }
        else{
            $destinationcharges = $r->freightInfos[0]->destinationCharges;
            for($i=0;$i<count($destinationcharges);$i++){
                $abc = $destinationcharges[$i];
               $abc->chargeId = $i;
               $destinationcharges[$i] = $abc;
            }
        }
        $onelinerates['additionalCosts'] = array_merge($freightcharges, $origincharges, $destinationcharges);

        $onelinerates['freightCharges'] = $freightcharges;
        $onelinerates['originCharges'] = $origincharges;
        $onelinerates['destinationCharges'] = $destinationcharges;

        //$onelinerates['additionalCosts'] = array();
        $onelinerates['totalPrice'] = $r->totalPrice;

        $onelinerates['vgmCutoff']= $r->freightInfos[0]->vgmCutoff;
        $onelinerates['docCutoff']= $r->freightInfos[0]->docCutoff;
        $onelinerates['cyCutoff']= $r->freightInfos[0]->cyCutoff;
        $onelinerates['departures'] = $r->freightInfos[0]->departures;


                    //$onerates = array_unique($onelinerates);
             $rates->push($onelinerates);
                 $i++;
                }
  //---- end adding online rates

   $token = $request->token;
   if($token!=NULL){
    $user = DB::table('tb_users')->select('username','email','remember_token')->where('remember_token','=',$token)->get();
    if($user->isNotEmpty()){
        $customer = DB::table('customer')->select('name','email','phone','contact_person')->where('email','=',$user[0]->email)->get();
        if($user->isNotEmpty() && $customer->isNotEmpty()){
            $searchhistory = Search_history::create([
                'name' => $user[0]->username,
                'phone' => $customer[0]->phone,
                'email' => $user[0]->email,
                'companyName' => $customer[0]->name,
                'contact_person' => $customer[0]->contact_person,
                'from_port' => $request->from_port,
                'to_port' => $request->to_port,
                //'remember_token' => Str::random(60),
            ]);
            $searchhistory->save();
            return response()->json($rates);
            exit();
        }
    }
    else{
        $searchhistory = Search_history::create([
            'name' => $user[0]->username,
            'email' => $user[0]->email,
            'from_port' => $request->from_port,
            'to_port' => $request->to_port,
            //'remember_token' => Str::random(60),
        ]);
        $searchhistory->save();
        return response()->json($rates);
        exit();
    }
    }
    else{
        $searchhistory = Search_history::create([
            'name' => 'anonymous',
            'from_port' => $request->from_port,
            'to_port' => $request->to_port,
            //'remember_token' => Str::random(60),
        ]);
        $searchhistory->save();
        return response()->json($rates);
        exit();
    }
}

    public function port_code($portName){
        return $portName;
        $codes = Port_name::where('port_name','LIKE','%'.$portName.'%')->get();
        if(count($codes)!==0){
            return $codes[0]['port_code'];
        }
        else {
            return 'False';
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
    public function liverates(){
        $base_uri           = 'https://apis.cma-cgm.net/pricing/commercial/quotation/v2/quotations/search';
        $urlAccessToken     = 'https://auth.cma-cgm.com/as/token.oauth2';
        $response = $client->get('https://apis.cma-cgm.net/pricing/commercial/quotation/v2/quotations/search', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . session('token'),
            ],
        ]);
    }

    public function cma_rates($from_port, $to_port){
        $reauth_client = new Client([
            // URL for access_token request
            'base_uri' => 'https://auth.cma-cgm.com/as/token.oauth2',
        ]);
        $reauth_config = [
            "client_id"         => "beapp-nebiar",
            "grant_type" => "client_credentials",
            "client_secret" => "CH9jwuggOswm2UArgKvNs88AnE0BSdlXigbg52lvhTkkG56kIwQUED2MjOpWT91p",
            "scope" => "quotation:be"
        ];
        $grant_type = new ClientCredentials($reauth_client, $reauth_config);
        $oauth = new OAuth2Middleware($grant_type);

        $stack = HandlerStack::create();
        $stack->push($oauth);

        $client = new Client([
            'handler' => $stack,
            'auth' => 'oauth',
        ]);
        $request = $client->get('https://apis.cma-cgm.net/pricing/commercial/quotation/v2/quotations/search', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer eyJhbGciOiJSUzI1NiIsImtpZCI6InNpZ25pbmctYXV0aC0yMDIyIiwieDV0IjoiX3diQWNXS0pNVGw4WUFET1h3RzJDbzZlOUxJIiwicGkuYXRtIjoiMiJ9.eyJzY29wZSI6WyJxdW90YXRpb246YmUiXSwiY2xpZW50X2lkIjoiYmVhcHAtbmViaWFyIiwiZXhwIjoxNjYxOTU5Mzg0fQ.uZ2DMmAF6pU2NSQLXQXhfZ5uRU6JiRKVoo6OG7_xMHxz4_x6lUJOYw30F-_IV8jPjloCgCUyYD9pomjydNx6kKvSulY_HwU4iRWTWji2q10epn_pPRN64KcO5PbxSJ3i_IKJQs5evIfMegTHXO_YDLIWnXGqX8wSsyF_Cg5WtKOoBsqYzCPoReWRGhLu6HpqIliNCpSLODB9JFpHL07Eod_qZO97VPzK8SCiUBU_XmmP-GXq2IHiK3EYlJzMmPjiC_DSBww9tUnI35iEoUqa1YO29Nj6zdkWDW8K0Ju916WFbxwZ8EMrQRYjL74UeRl7cdfwiEO9GUY6bqSydxsF7g',
            ],
            'multipart' => [
                [
                  'name' => 'portOfLoading',
                  'contents' => $from_port
                ],
                [
                  'name' => 'portOfDischarge',
                  'contents' => $to_port
                ]
            ]
        ]);

        try {
            $response = json_decode($request->getBody()->getContents());
            $i=0;
                $livedata = array();
                foreach($response as $res){
                    $baserate = $res->equipmentAndBasedRates[0]->basedRate->basicOceanFreightRate;
                    $margin = 100;
                    $total = $baserate+$margin;
                    $livedata[$i]['id'] = 'CMA'.$res->quoteLineId;
                    $livedata[$i]['sl_name'] = "CMA (live)";
                    $livedata[$i]['from_port'] = $this->port_name($from_port);
                    $livedata[$i]['to_port'] = $this->port_name($to_port);
                    $livedata[$i]['cargo_size'] = strtolower($res->equipmentAndBasedRates[0]->equipmentGroupIsoCode);
                    $livedata[$i]['base_rate'] = $baserate;
                    $livedata[$i]['Margin'] = $margin;
                    $livedata[$i]['total'] = $total;
                    $livedata[$i]['FAF'] = "";
                    $livedata[$i]['seal_charge'] = "";
                    $livedata[$i]['ECC'] = "";
                    $livedata[$i]['service_mode'] = "";
                    $livedata[$i]['direct_via'] = "";
                    $livedata[$i]['via_port'] = "";
                    $livedata[$i]['transit_time'] = "";
                    $livedata[$i]['expiry_date'] = $res->validityto;
                    $livedata[$i]['sl_logo'] =  "https://backend.transpost.co/logos/cma_live.png";
                    $livedata[$i]['remarks'] =  "<li>Origin - Charges payable at Export</li>
                                                 <li>Destination - Charges payable at Import</li>";
                    $livedata[$i]['terms'] =  "<h2>Access the link below to understand Terms and Conditions - CMA CGM
                    (https://www.cma-cgm.com/ebusiness/registration/terms-and-conditions)</h2>";

                    $livedata[$i]['additionalCosts'] = [];

                }
                // dd($response);

        return $livedata;
        } catch (ClientErrorResponseException $exception) {
            return  $exception->getResponse()->getBody(true);
        }
        //$res = $client->sendAsync($request, $options)->wait();

        //$data = $resp['equipmentAndBasedRates'];
                // $resp[0]->equipmentAndBasedRates <---base rates
                // "ID": 500,
                // "sl_name": "COSCO",
                // "from_port": "Jawaharlal Nehru",
                // "to_port": "COLOMBO",
                // "_40gp": "",
                // "Margin": 150,
                // "FAF": "109",
                // "seal_charge": "5",
                // "ECC": "",
                // "service_mode": "",
                // "direct_via": "",
                // "via_port": "",
                // "transit_time": "",
                // "expiry_date": "2022-09-15 12:36:57",
                 //$cma_live_data = $this->cma_rates($from_port_code, $to_port_code);



        //echo "Status: ".$request->getStatusCode()."\n";
    }
    public function pdf(Request $request)
    {
        //$data = file_get_contents(public_path() . "/json/rates.json");
        //$customer = json_decode($data, true);
        //dd($customer);
        $pdf = PDF::loadView('pdf', ['customer' => $request]);
        $string = Str::random(8);
       Storage::disk('quotes')->put($string.'.pdf', $pdf->output());
        $filename = ($string.'.pdf');
        //$pdf->SetTitle('Tranpost');

        return $pdf->stream($filename);
        //return response()->json($filename);
    }

    public function oneline_rates($pol, $pod, $equipnmentName, $equipmentSize, $equipmentIsoCode, $equipmentONECntrTpSz){
        $ch = curl_init();
        //dd($equipmentSize);
        curl_setopt($ch, CURLOPT_URL, 'http://146.190.53.191:3000/process_post');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'pol='.$pol.'&pod='.$pod.'&equipnmentName='.$equipnmentName.'&equipmentSize='.$equipmentSize.'&equipmentIsoCode='.$equipmentIsoCode.'&equipmentONECntrTpSz='.$equipmentONECntrTpSz);

        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        //dd($ch);
        $response = json_decode($result);
        curl_close($ch);
        return $response;

    }

    public function cargotype($cargo_type){

    }
     /* @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Rates  $rates
     * @return \Illuminate\Http\Response
     */
    public function show(Rates $rates)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Rates  $rates
     * @return \Illuminate\Http\Response
     */
    public function edit(Rates $rates)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Rates  $rates
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rates $rates)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Rates  $rates
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rates $rates)
    {
        //
    }
}
