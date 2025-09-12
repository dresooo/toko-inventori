<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'no_telp' => 'required|digits_between:10,13|unique:users,no_telp',
            'alamat' => 'required|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // otomatis hash karena cast 'password' => 'hashed'
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // otomatis hash
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'user_type' => 'customer', // selalu default customer
        ], 201);
    }

    // LOGIN
    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Email/Password Salah'], 401);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    // Tentukan halaman berdasarkan user_type
    $redirect_url = $user->user_type === 'admin' ? '/dashboard' : '/homepage';
    return response()->json([
        'message' => 'User logged in',
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => $user,
        'redirect_url' => $redirect_url // front-end pakai ini untuk arahkan user
    ]);
}

    // GET USER
    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    // LOGOUT
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    // UPDATE USER
public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    // validasi input
    $request->validate([
        'name'   => 'required|string|max:255',
        'email'  => 'required|email|unique:users,email,' . $id,
        'no_telp'=> 'nullable|digits_between:10,13|unique:users,no_telp,' . $id,
        'alamat' => 'nullable|string|max:255',
        'password' => 'nullable|string|min:6|confirmed',
    ]);

    // update data
    $user->name   = $request->name;
    $user->email  = $request->email;
    $user->no_telp= $request->no_telp;
    $user->alamat = $request->alamat;

    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    $user->save();

    return response()->json([
        'message' => 'Profil berhasil diupdate',
        'user' => $user
    ], 200);
}
}
