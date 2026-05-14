<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
use App\Services\Response;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request)
    {
        $user = User::create($request->validated());

        //make token
        $token = $user->createToken('auth_token')->plainTextToken;

        return Response::success([
            'token'=>$token,
            'user' => $user->toResource()
        ]);
    }
}
