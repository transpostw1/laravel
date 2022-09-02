<?php

namespace App\Http\Controllers;

use App\Models\Rates;
use App\Models\Port_name;
use Illuminate\Http\Request;
use Carbon\Carbon;
use GuzzleHttp\Client;

use kamermans\OAuth2\GrantType\ClientCredentials;
use kamermans\OAuth2\OAuth2Middleware;
use kamermans\OAuth2\GrantType\PasswordCredentials;
use GuzzleHttp\HandlerStack;
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

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
        
        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        
            exit(0);
        }


        $endDate = Carbon::now()->addMonths(3);
        $startDate = Carbon::now();
       
        $rates = Rates::where('from_port', $request->from_port)
                ->where('to_port', $request->to_port)
                ->whereBetween('expiry_date', [$startDate, $endDate])
                ->get();
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
                dd('cache reset');
               $from_port_code =  $this->port_code($request->from_port);
               $to_port_code =  $this->port_code($request->to_port);
                $cma_live_data = $this->cma_rates($from_port_code, $to_port_code);
               //array_push($rates, $cma_live_data);
               foreach($rates as $rate){
                $rate['base_rate'] = $rate["_".$cargo_type];
                $rate['Margin'] = 0;
                $rate['total'] = $rate["_".$cargo_type];
                $rate['cargoSize'] = $cargo_type;
               }
               foreach($cma_live_data as $v){
                $rates[] = $v;
               }
               
            //    print_r();exit;
            //    $cma_rates = json_decode($this->cma_rates($from_port_code, $to_port_code), true, JSON_UNESCAPED_SLASHES);
            //    $ratesarr = array(
            //     'sheetRates' => $rates,
            //     'liveRates'=> $cma_rates,
            //    );
               return response()->json($rates);
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
        //$res = $client->sendAsync($request, $options)->wait();
        $response = json_decode($request->getBody()->getContents());
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
                 
                $i=0;
                foreach($response as $res){
                    $baserate = $res->equipmentAndBasedRates[0]->basedRate->basicOceanFreightRate;
                    $margin = 100;
                    $total = $baserate+$margin;

                    $livedata[$i]['sl_name'] = "CMA (live)";
                    $livedata[$i]['from_port'] = $this->port_name($from_port);
                    $livedata[$i]['to_port'] = $this->port_name($to_port);
                    $livedata[$i]['cargoSize'] = $res->equipmentAndBasedRates[0]->equipmentGroupIsoCode;
                    $livedata[$i]['base_rate'] = $baserate;
                    $livedata[$i]['Margin'] = $margin;
                    $livedata[$i]['Total'] = $total;
                    $livedata[$i]['FAF'] = "";
                    $livedata[$i]['seal_charge'] = "";
                    $livedata[$i]['ECC'] = "";
                    $livedata[$i]['service_mode'] = "";
                    $livedata[$i]['direct_via'] = "";
                    $livedata[$i]['via_port'] = "";
                    $livedata[$i]['transit_time'] = ""; 
                    $livedata[$i]['expiry_date'] = $res->validityto;
                    $livedata[$i]['sl_logo'] =  "http://launchindia.org/transpost/logos/cma_live.png";
                    $livedata[$i]['remarks'] =  "<li>Origin - Charges payable at Export</li>
                                                 <li>Destination - Charges payable at Import</li>";
                    $livedata[$i]['terms'] =  "<h2>Access the link below to understand Terms and Conditions - CMA CGM
                    (https://www.cma-cgm.com/ebusiness/registration/terms-and-conditions)</h2>";
                    
                }
                // dd($response);
        return $livedata;

        //echo "Status: ".$request->getStatusCode()."\n";
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
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
