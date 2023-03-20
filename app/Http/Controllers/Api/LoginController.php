<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BaseController as BaseController;
use Laravel\Passport\Passport;
use Carbon\Carbon;

class LoginController extends BaseController
{
    public function Login(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
            'password'=>'required|string'
        ]);
        // $credentials = $request->validate([
        //     'email' => ['required', 'email'],
        //     'password' => ['required']
        // ]);
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials))
        {
            return response()->json([
                'status' => 'failed',
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = $request->user();
        $tokenResult = $user->createToken('Porsonal Access Token');
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();

        return response()->json([
            'status' => 'success',
            'user'=>Auth::user(),
            'access_token' => $tokenResult->accessToken,
            'token_type'=>'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            'message' => 'Logged In Successfully'
        ], 200);

    }
}
