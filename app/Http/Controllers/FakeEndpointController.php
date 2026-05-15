<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class FakeEndpointController extends Controller
{

    public function __invoke(Request $request)
    {
        //make fake Res
        $resCodes = [200, 404, 403, 422, 500];
        $randomCode = $resCodes[array_rand($resCodes)];
        return \response()->json([
            'code' => $randomCode,
            'message' => 'Server response ... ' . $randomCode,
            'received' => $request->all(),
        ], $randomCode);

    }

}
