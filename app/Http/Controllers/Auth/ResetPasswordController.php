<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Log;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    // protected $redirectTo = RouteServiceProvider::HOME;

    public function reset(Request $request)
    {
        $request->validate($this->rules(), $this->validationErrorMessages());
        $user_model = User::where('email', $request->email)->first();
        $user_model->fill([
            'password' => Hash::make($request->password),
            'api_token' => $request->token,
        ]);
        $user_model->save();
        $url = 'https://gamer-lab.net/login';
        header('Location: ' . $url, true, 301);
        exit;
    }
}
