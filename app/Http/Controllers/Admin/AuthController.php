<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Display Login view
     */
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Support'])) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    /**
     * Authenticate credential requests
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            
            // Restrict access to roles with administration access
            if ($user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Support'])) {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard')->with('success', 'Welcome back, ' . $user->name . '!');
            }
            
            // If standard Customer attempts login, discard session
            Auth::logout();
            return redirect()->back()->withInput()->with('error', 'Access Denied: You do not hold administrative privileges.');
        }

        return redirect()->back()->withInput()->with('error', 'Invalid security credentials.');
    }

    /**
     * Display Sandbox Forgot Password recovery view
     */
    public function showForgotPassword()
    {
        return view('admin.auth.forgot-password');
    }

    /**
     * Send Sandbox password reset URL on screen
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Guard against resetting standard customer password from here
            if (!$user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Support'])) {
                return redirect()->back()->with('error', 'This account is not registered as an administrator.');
            }

            // Generate mock recovery token
            $token = Str::random(40);
            $resetUrl = route('admin.reset-password', ['email' => $request->email, 'token' => $token]);

            // Flash successful sandbox pass notification to developer
            return redirect()->back()->with('success_raw', "
                <strong>Sandbox Mail Simulator:</strong> Recovery link generated successfully!<br>
                Please use this sandbox link to set your new password:<br>
                <a href='{$resetUrl}' class='inline-block mt-3 px-4 py-2 bg-purple-600 text-white rounded-lg text-xs font-semibold hover:bg-purple-500 transition-colors'>Reset Password Now &rarr;</a>
            ");
        }

        return redirect()->back()->with('error', 'We could not find an administrator registered with that email.');
    }

    /**
     * Display Password Reset view
     */
    public function showResetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
        ]);

        return view('admin.auth.reset-password', [
            'email' => $request->email,
            'token' => $request->token,
        ]);
    }

    /**
     * Reset account password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->route('admin.login')->with('error', 'Invalid recovery session.');
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('admin.login')->with('success', 'Password reset successfully! Please sign in using your new credentials.');
    }

    /**
     * Update the currently authenticated administrator profile info & password.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->back()->with('error', 'Session expired.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_image);
            }
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Your administrator profile settings have been updated successfully!');
    }
}
