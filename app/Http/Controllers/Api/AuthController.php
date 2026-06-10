<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nim' => 'nullable|string|unique:users',
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|min:11|max:13',
            'role' => 'in:admin,seller,buyer'
        ]);

        $user = User::create([
            'nim' => $validated['nim'] ?? null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'role' => $validated['role'] ?? 'buyer'
        ]);

        return response()->json([
            'data' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'nim' => 'required|string',
            'password' => 'required'
        ]);

        $user = User::where('email', $validated['email'])->where('nim', $validated['nim'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'data' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken
        ]);
    }

    public function user(Request $request)
    {
        return response()->json(['data' => $request->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $user->update($validated);

        return response()->json(['data' => $user]);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Password saat ini salah'], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json(['message' => 'Password berhasil diperbarui']);
    }

    public function updatePhoto(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('users', 'public');
            $user->update(['photo' => $path]);
            return response()->json(['photo' => $path]);
        }

        return response()->json(['message' => 'Gagal upload foto'], 400);
    }

    public function updateKtm(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'ktm' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('ktm')) {
            $path = $request->file('ktm')->store('ktm', 'public');
            $user->update(['ktm' => $path]);
            return response()->json(['ktm' => $path]);
        }

        return response()->json(['message' => 'Gagal upload KTM'], 400);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
