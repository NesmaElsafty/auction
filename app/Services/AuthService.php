<?php

namespace App\Services;

use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuthService
{
    public function register($data)
    {
        $user = User::create([
            'name' => $data['name'],
            'national_id' => $data['national_id'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'summary' => $data['summary'] ?? null,
            'link' => $data['link'] ?? null,
            'password' => Hash::make($data['password']),
            'type' => $data['type'],
            'is_active' => true,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function login($nationalId, $password, $type)
    {
        $user = User::where('national_id', $nationalId)->where('type', $type)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        if (!$user->is_active) {
            throw new \Exception('Account is inactive');
        }

        // Revoke all existing tokens
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(User $user)
    {
        $user->currentAccessToken()->delete();
    }

    public function profile(User $user)
    {
        return $user;
    }

    public function updateProfile($user, $data)
    {
        // dd($data);
        $user->update($data);
        return $user;
    }

    public function refreshToken(User $user)
    {
        // Revoke current token
        $user->currentAccessToken()->delete();

        // Create new token
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function forgetPassword($nationalId)
    {
        $user = User::where('national_id', $nationalId)->first();

        if (!$user) {
            throw new \Exception('User not found');
        }

        if (!$user->email) {
            throw new \Exception('Email not found for this user');
        }

        // Generate 6-digit OTP
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Delete old unused OTPs for this user
        PasswordResetOtp::where('national_id', $nationalId)
            ->where('is_used', false)
            ->delete();

        // Create new OTP
        PasswordResetOtp::create([
            'national_id' => $nationalId,
            'email' => $user->email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(10),
            'is_used' => false,
        ]);

        // Send OTP via email
        Mail::raw("Your password reset OTP is: {$otp}. This OTP will expire in 10 minutes.", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Password Reset OTP');
        });
    }

    /**
     * Reset password with OTP
     */
    public function resetPassword($nationalId, $otp, $newPassword)
    {
        $otpRecord = PasswordResetOtp::where('national_id', $nationalId)
            ->where('otp', $otp)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otpRecord) {
            throw new \Exception('Invalid or expired OTP');
        }

        $user = User::where('national_id', $nationalId)->first();

        if (!$user) {
            throw new \Exception('User not found');
        }

        DB::transaction(function () use ($user, $otpRecord, $newPassword) {
            // Update password
            $user->update([
                'password' => Hash::make($newPassword),
            ]);

            // Mark OTP as used
            $otpRecord->update([
                'is_used' => true,
            ]);

            // Revoke all existing tokens for security
            $user->tokens()->delete();
        });
    }

    public function verifyOtp($nationalId, $otp)
    {
        $otpRecord = PasswordResetOtp::where('national_id', $nationalId)
            ->where('otp', $otp)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();
        if (!$otpRecord) {
            throw new \Exception('Invalid or expired OTP');
        }

        $user = User::where('national_id', $nationalId)->first();
        if (!$user) {
            throw new \Exception('User not found');
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        $otpRecord->update([
            'is_used' => true,
        ]);
        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}

