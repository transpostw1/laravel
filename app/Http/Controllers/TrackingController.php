<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class TrackingController extends Controller
{
    public function tracking(Request $request){
        $ch = curl_init();
        $username = 'apilink%40freightify.com';
        $password = 'Alpha%40123';
        $grant_type = 'password';

        curl_setopt($ch, CURLOPT_URL, 'https://api.freightify.com/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'username='.$username.'&password='.$password.'&grant_type='.$grant_type.'&scope=');

        $headers = array();
        $headers[] = 'Authorization: Basic OTI2NTU2ZTAtNmNkNi0xMWVkLWFiNzctMzE3MDExMGJmODk1OktCQldkajNUTUYzNWt4OFk0R2ZaUWg4WGdRZjJuZWFqYkFqczR0WWo=';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        $response = json_decode($result);
        curl_close($ch);
        $token = get_object_vars($response)['access_token'];
        $ch1 = curl_init();
        $containernumber = 'FANU1639456';
        $sealine = 'HLCU';

        curl_setopt($ch1, CURLOPT_URL, 'https://api.freightify.com/v1/track/container/'.$containernumber.'?sealine='.$sealine);
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch1, CURLOPT_POST, 1);
        $headers1= array();
        $headers1[] = 'Authorization: Bearer '.$token;
        curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers1);
        $result1 = curl_exec($ch1);
        if (curl_errno($ch1)) {
            echo 'Error:' . curl_error($ch1);
        }
        $response1 = json_decode($result1);
        curl_close($ch1);
        //return $response1;
        $data = file_get_contents(public_path() . "/json/freightify.json");
        $freightify = json_decode($data, true);
        return $freightify;

    }
}
