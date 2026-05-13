<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Models\User;
use App\Services\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $data=$request->validated();
        //find User
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return Response::error(__('auth.failed'),401);
        }

        //make token
        $token = $user->createToken('auth_token')->plainTextToken;

        return Response::success([
            'token'=>$token,
        ]);
    }
}
