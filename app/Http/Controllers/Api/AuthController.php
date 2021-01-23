<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Log;
use GuzzleHttp\Client;
use DB;

class AuthController extends Controller
{
    public function login(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return [
                'errors' => $validator->messages(),
            ];
        }

        $email = $request->email;
        $password = $request->password;
        $user = User::where("email",$email)->first();
        if ($user && Hash::check($password, $user->password)) {
            $token = Str::random(60);
            $user->api_token = $token;
            $user->save();
            return [
                "token" => $token,
                "user" => $user
            ];
        }else{
            return abort(401);
        }
    }

    public function register(Request $request){
        
        DB::beginTransaction();

        try {
            $validator = Validator::make($request->all(), [
                'platform' => 'required|max:20',
                'player_id' => 'required|max:30',
                'email' => 'max:30|string|email|max:255|unique:users',
                'password' => 'required|string',
            ]);
            if ($validator->fails()) {
                return [
                    'errors' => $validator->messages(),
                ];
            }

            $platform = $request->platform;
            $player_id = $request->player_id;
            $email = $request->email;
            $password = $request->password;
            $token = Str::random(60);

            if($platform == 'all'){
                $platformUserId = $player_id;
            } else {
                $client = new Client;
                $base_url = 'https://public-api.tracker.gg/v2/apex/standard/profile/'.$platform.'/'.$player_id;
                $response = $client->request('GET', $base_url, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'TRN-Api-Key' => \config('token.apex_key'),
                    ],
                    'http_errors' => false,
                ]);
                $responseCode = $response->getStatusCode();
                if($responseCode == 200){
                    $response = json_decode($response->getBody()->getContents(), true);
                    $platformUserId = $response['data']['platformInfo']['platformUserId'];
                } elseif($responseCode == 404){
                    return [
                        'errors' => ['ユーザーが見つかりませんでした。'],
                    ];
                }
            }

            $user = User::where("email",$email)->first();
            if (!$user) {
                $user_model = new User;
                $user_model->fill([
                    'name' => $platformUserId,
                    'platform' => $platform,
                    'player_id' => $player_id,
                    'email' => $email,
                    'password' => Hash::make($password),
                    'api_token' => $token,
                ]);
                $user_model->save();
                DB::commit();
                return [
                    "token" => $token,
                    "user" => $user_model
                ];
            }else{
                return abort(401);
            }
        } catch (Exception $e) {
            DB::rollback();
            return abort(401);
        }
    }
}
