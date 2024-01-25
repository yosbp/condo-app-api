<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Seller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        //Validated
        $validateUser = Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'email' => 'required|email|string|unique:users,email',
                'password' => 'required',
                'role' => 'required|in:admin,client',
            ]
        );

        // Message if validation fails
        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 401);
        }

        // Create only 1 admin user
        if ($request->role === 'admin') {
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                return response()->json([
                    'status' => false,
                    'message' => 'There is already an admin user',
                ], 401);
            }
        }

        // Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role'  => $request->role,
        ]);

        // Return response
        return response()->json([
            'status' => true,
            'message' => 'User Created Successfully',
            'token' => $user->createToken("TOKEN")->plainTextToken,
            'user' => $request->only('name', 'role'),
        ], 200);
    }

    public function login(Request $request)
    {
        //Validated
        $validateUser = Validator::make(
            $request->all(),
            [
                'email' => 'required|email|string|exists:users,email',
                'password' => [
                    'required'
                ],
            ]
        );

        $validateUser->setCustomMessages([
            'email.exists' => 'El email no existe en la base de datos',
        ]);

        // Message if validation fails

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 401);
        };

        // Check if user exists

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'The Provided credentials are not correct',
            ], 401);
        };

        // Get user data

        $user = User::where('email', $request->email)->first();

        return response()->json([
            'status' => true,
            'message' => 'Loggin Successfully',
            'token' => $user->createToken("TOKEN")->plainTextToken,
            'user' => $user->only('name', 'email', 'role'),
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message' => 'Logged out successfully',
        ], 200);
    }
}
