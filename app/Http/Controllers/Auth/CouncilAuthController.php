<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\CouncilMember;
use App\Http\Controllers\Controller;

class CouncilAuthController extends Controller
{
    public function register(Request $request)
    {
        $admin = Auth::guard('council')->user();

        if (! $admin || $admin->role !== 'admin') {
            return response()->json([
                'message' => 'Apenas administradores podem registrar novos conselheiros.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:council_members,email'],
            'password' => ['required', 'min:6'],
            'role' => ['required', 'in:admin,member']
        ]);

        $member = CouncilMember::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role']
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

        $council = CouncilMember::where('email', $request->email)->first();

        if (! $council || ! Hash::check($request->password, $council->password)) {
            return response()->json(['message' => 'Caro Usuario do conselho, infelizmente o sistema informa que as informações fornecidas não são válidas'], 401);
        }

        $token = $council->createToken('council-token')->plainTextToken;

        return response()->json([
            'token' => $token
        ]);
    }
}
