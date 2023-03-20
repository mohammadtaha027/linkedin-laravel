<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BaseController as BaseController;
use Laravel\Passport\Passport;
use Carbon\Carbon;

class RegisterController extends BaseController
{
    public function Register (Request $request)
    {
        $request -> validate([
            'name' => 'required|string|min:3|max:50',
            'email'=>'required|email|unique:users',
            'password' => 'required|string|confirmed',
            'confirm_password' => 'required'
        ]);
        $input = $request->all();
        $register = User::create([
            'name'=> $input['name'],
            'email'=> $input['email'],
            'password' => Hash::make($input['password'])
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Registration Success'
        ], 200);

    }
}
