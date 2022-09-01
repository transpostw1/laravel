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

    public function select(Request $request,){
        $endDate = Carbon::now()->addMonths(3);
        $startDate = Carbon::now();
       
        $rates = Rates::where('from_port', $request->from_port)
                ->where('to_port', $request->to_port)
                ->whereBetween('expiry_date', [$startDate, $endDate])
                ->get();
                $cargo_type = $request->cargo_type;
                if($cargo_type==='_40hc'){
                  $rates->makeHidden(['_20gp', '_40gp']);
                }
                else if($cargo_type==='_40gp'){
                    $rates->makeHidden(['_20gp', '_40hc']);
                }
                else if($cargo_type==='_20gp'){
                    $rates->makeHidden(['_40hc', '_40gp']);
                }
                else{
                    $rates->makeHidden();
                }
                
               $from_port_code =  $this->port_code($request->from_port);
               $to_port_code =  $this->port_code($request->to_port);

               //print_r($this->cma_rates($from_port_code, $to_port_code));exit;
               $cma_rates = json_decode($this->cma_rates($from_port_code, $to_port_code), true, JSON_UNESCAPED_SLASHES);
               $ratesarr = array(
                'sheetRates' => $rates,
                'liveRates'=> $cma_rates,
               );
               return response()->json($ratesarr);
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
        return $request->getBody()->getContents();
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
