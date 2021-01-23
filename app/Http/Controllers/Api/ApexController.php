<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Log;

class ApexController extends Controller
{
    public function post(Request $request){
        Log::debug($request);
    }
}
