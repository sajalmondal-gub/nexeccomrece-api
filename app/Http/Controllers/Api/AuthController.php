<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Register a new customer user and issue a Bearer token.
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign default Customer role using Spatie Permission
        $customerRole = Role::where('name', 'Customer')->first();
        if ($customerRole) {
            $user->assignRole($customerRole);
        }

        // Generate Sanctum access token
        $token = $user->createToken('customer_access_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Account created successfully!',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_image' => $user->profile_image ? asset('storage/' . $user->profile_image) : null,
                    'roles' => ['Customer'],
                    'permissions' => [],
                ],
                'token' => $token,
            ]
        ], 201);
    }

    /**
     * Authenticate customer credentials and return a secure token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials provided.'
            ], 401);
        }

        // Generate Sanctum access token
        $token = $user->createToken('customer_access_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Logged in successfully!',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_image' => $user->profile_image ? asset('storage/' . $user->profile_image) : null,
                    'roles' => $user->getRoleNames(),
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                ],
                'token' => $token,
            ]
        ]);
    }

    /**
     * Fetch the currently authenticated customer profile details.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_image' => $user->profile_image ? asset('storage/' . $user->profile_image) : null,
                    'roles' => $user->getRoleNames(),
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                ]
            ]
        ]);
    }

    /**
     * Update the authenticated customer profile information and upload photo.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'profile_image' => 'nullable|string',
        ]);

        if ($request->has('profile_image') && $request->profile_image) {
            $imgData = $request->profile_image;
            if (preg_match('/^data:image\/(\w+);base64,/', $imgData, $type)) {
                $imgData = substr($imgData, strpos($imgData, ',') + 1);
                $extension = strtolower($type[1]);
            } else {
                $extension = 'png';
            }
            $imgData = base64_decode($imgData);
            if ($imgData !== false) {
                $filename = 'profile_' . $user->id . '_' . time() . '.' . $extension;
                \Illuminate\Support\Facades\Storage::disk('public')->put('profile_images/' . $filename, $imgData);
                $user->profile_image = 'profile_images/' . $filename;
            }
        }

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully!',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_image' => $user->profile_image ? asset('storage/' . $user->profile_image) : null,
                    'roles' => $user->getRoleNames(),
                    'permissions' => $user->getAllPermissions()->pluck('name'),
                ]
            ]
        ]);
    }

    /**
     * Revoke the currently active API access token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully!'
        ]);
    }

    /**
     * Delete the authenticated customer account.
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Revoke all customer tokens
        $user->tokens()->delete();
        
        // Delete user row
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Your account has been deleted permanently.'
        ]);
    }

    /**
     * Send Sandbox password reset URL for customer in mobile app.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Generate mock recovery token
            $token = Str::random(40);
            
            // Generate sandbox reset link
            $resetUrl = route('admin.reset-password', ['email' => $request->email, 'token' => $token]);

            return response()->json([
                'status' => 'success',
                'message' => 'Password reset simulation successful!',
                'data' => [
                    'email' => $request->email,
                    'token' => $token,
                    'reset_url' => $resetUrl,
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'We could not find an account registered with that email.'
        ], 404);
    }
}
