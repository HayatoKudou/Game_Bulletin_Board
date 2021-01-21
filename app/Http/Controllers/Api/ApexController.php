<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Log;

class ApexController extends Controller
{
    public function playersProfileStats(){
        $base_url = 'https://public-api.tracker.gg/v2/apex/standard/profile/psn/itoenn58';
        $client = new Client;
        $response = $client->request('GET', $base_url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'TRN-Api-Key' => \config('token.apex_key'),
            ],
        ]);
        return $response->getBody();;
    }

    public function playerStatistics(){
        $client = new Client;
        $base_url = 'https://api.mozambiquehe.re/bridge?version=4&platform=PS4&player=itoenn58&auth='.\config('token.apex_key_2');
        $response = $client->request('GET', $base_url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);
        return $response->getBody();
        Log::debug($response);
    }

    public function searchApexPlayer(Request $request){
        $client = new Client;
        $base_url = 'https://public-api.tracker.gg/v2/apex/standard/profile/psn/'.$request->playerName;
        $response = $client->request('GET', $base_url, [
            'headers' => [
                'Content-Type' => 'application/json',
                'TRN-Api-Key' => \config('token.apex_key'),
            ],
            'http_errors' => false,
        ]);
        $responseCode = $response->getStatusCode();
        Log::debug('code: '.$responseCode);
        if($responseCode == 200){
            return $response->getBody();
        } elseif($responseCode == 404){
            return '404';
        }
    }
}
