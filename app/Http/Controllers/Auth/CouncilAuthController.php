<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\CouncilMember;

class CouncilAuthController
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:council_members,email'],
            'password' => ['required', 'min:6']
        ]);

        $member = CouncilMember::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $member->createToken('council-token')->plainTextToken;

        return response()->json([
            'message' => 'Conselheiro registrado com sucesso',
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 201);
    }
    
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
