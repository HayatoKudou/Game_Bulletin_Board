<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Article;
use App\Models\Apex_player;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Log;
use GuzzleHttp\Client;
use DB;
use Mail;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Password;


class AuthController extends Controller
{
    use SendsPasswordResetEmails;

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
            $articles = Article::orderBy('created_at', 'desc')->get();
            return [
                "token" => $token,
                "user" => $user,
                "articles" => $articles,
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
                'email' => 'string|email|max:255|unique:users',
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

                //apex_playerに登録
                $apex_player_mode = new Apex_player;
                $apex_player_mode->fill([
                    'user_id' => $user_model->id,
                ]);
                if ($platform !== 'all') {
                    $apex_player_mode->fill([
                        'platformSlug' => $response['data']['platformInfo']['platformSlug'],
                        'platformUserId' => $response['data']['platformInfo']['platformUserId'],
                        'avatarUrl' => $response['data']['platformInfo']['avatarUrl'],
                        'level' => $response['data']['segments'][0]['stats']['level']['value'],
                        'kills' => $response['data']['segments'][0]['stats']['kills']['value'],
                        'damage' => $response['data']['segments'][0]['stats']['damage']['value'],
                        'rankScore' => $response['data']['segments'][0]['stats']['rankScore']['value'],
                        'rankScore_iconUrl' => $response['data']['segments'][0]['stats']['rankScore']['metadata']['iconUrl'],
                    ]);
                }
                $apex_player_mode->save();

                DB::commit();
                $articles = Article::orderBy('created_at', 'desc')->get();
                return [
                    "token" => $token,
                    "user" => $user_model,
                    "articles" => $articles,
                    "apex_player" => $apex_player_mode,
                ];
            }else{
                return abort(401);
            }
        } catch (Exception $e) {
            DB::rollback();
            return abort(401);
        }
    }

    //リセットメール送信
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'max:255|string|email',
        ]);
        if ($validator->fails()) {
            return [
                'errors' => $validator->messages(),
            ];
        }
        $user_model = User::where('email', $request->email)->exists();
        if(!$user_model){
            return [
                'errors' => ['メールアドレスが登録されていません。'],
            ];
        }
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );
        return $response == Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link sent to your email.', 'status' => true], 201)
            : response()->json(['message' => 'Unable to send reset link', 'status' => false], 401);
    }

    public function contact(Request $request){
        Log::debug($request);
        try {
            $validator = Validator::make($request->all(), [
                'contents' => 'required|max:255',
            ]);
            if ($validator->fails()) {
                return [
                    'errors' => $validator->messages(),
                ];
            }
            Mail::send('emails.mail', [
                'email' => $request->email,
                'contents' => $request->contents,
            ], function($message){
                $message->to('kudoh115@gmail.com')
                ->from('hayatoportfolio@gmail.com')
                ->subject('gamer-lab.netからのお問い合わせ');
            });
            return [
                'message' => 'お問い合わせを受け付けました。'
            ];
        } catch (Exception $e) {
            return abort(401);
        }
    }

}
