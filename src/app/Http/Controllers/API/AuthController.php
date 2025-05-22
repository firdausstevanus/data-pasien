<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Login API dengan token authentication
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'nullable|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial yang diberikan tidak cocok dengan data kami.'],
            ]);
        }

        // Token memiliki waktu kadaluarsa
        $token = $user->createToken(
            $request->device_name ?? 'api_token', 
            ['*'], 
            Carbon::now()->addDays(30)
        )->plainTextToken;

        // Rekam log aktivitas login
        activity()
            ->causedBy($user)
            ->log('login api');

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'Login berhasil',
        ]);
    }

    /**
     * Logout API dan hapus token
     */
    public function logout(Request $request)
    {
        // Hapus token saat ini
        $request->user()->currentAccessToken()->delete();

        // Rekam log aktivitas logout
        activity()
            ->causedBy($request->user())
            ->log('logout api');

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }

    /**
     * Mendapatkan data user saat ini
     */
    public function me(Request $request)
    {
        $user = $request->user();
        $roles = $user->getRoleNames();

        return response()->json([
            'user' => $user,
            'roles' => $roles,
        ]);
    }
}
