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
        $response = json_decode($result,true);
        $access_token = var_dump($response['access_token']);
        curl_close($ch);
        return $access_token;
    }
}
