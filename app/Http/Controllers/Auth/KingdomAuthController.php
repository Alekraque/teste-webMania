<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Kingdom;

class KingdomAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        $kingdom = Kingdom::where('email', $request->email)->first();

        if (! $kingdom || ! Hash::check($request->password, $kingdom->password)) {
            return response()->json([
                'message' => 'Caro Reino, infelizmente o sistema informa que as informações fornecidas não são válidas'
            ], 401);
        }

        $token = $kingdom->createToken('kingdom-token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
}
