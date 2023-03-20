<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
                'message' => 'Login info is incorrect'
            ], 200);
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
    public function Register (Request $request)
    {
        $input = $request->validate([
            'name' => 'required|string|min:3|max:50',
            'email'=>'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
        User::create([
            'name'=> $input['name'],
            'email'=> $input['email'],
            'password' => Hash::make($input['password'])
        ]);
        return response()->json(['status' => 'success', 'message' => 'Registration Success'], 200);

    }
    public function getUserDetails()
    {
        if(Auth::guard('api')->check()){
            $user = Auth::guard('api')->user();
            return response()->json(['data'=>$user], 200);
        }
        return response()->json(['data'=>'Unauthenticated'], 200);
    }
    public function userLogout()
    {
        if(Auth::guard('api')->check()){
            $accessToken = Auth::guard('api')->user()->token();
            DB::table('oauth_refresh_tokens')
            ->where('access_token_id', $accessToken->id)
            ->update(['revoked'=> true]);
            $accessToken->revoke();
            return response()->json(['data'=>'Unauthenticated', 'message'=>'User Logged out'], 200);
        }
        return response()->json(['data'=>'false', 'message'=>'Can\'t find user'], 200);

    }
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }

}
