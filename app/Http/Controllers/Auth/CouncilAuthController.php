<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\CouncilMember;

class CouncilAuthController
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        $kingdom = Kingdom::where('email', $request->email)->first();

        if (! $kingdom || ! Hash::check($request->password, $kingdom->password)) {
            return response()->json(['message' => 'Caro Usuario do conselho, infelizmente o sistema informa que as informações fornecidas não são válidas'], 401);
        }

        $token = $kingdom->createToken('kingdom-token')->plainTextToken;

        return response()->json([
            'token' => $token
        ]);
    }
}
