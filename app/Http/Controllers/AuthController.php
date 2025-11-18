<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'national_id' => 'required|string|unique:users,national_id',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:255',
                'summary' => 'nullable|string',
                'link' => 'nullable|url|max:255',
                'password' => 'required|string|min:6',
                'type' => 'required|in:user,admin',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->authService->register($request->all());

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => new UserResource($result['user']),
                    'token' => $result['token'],
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'national_id' => 'required|string',
                'password' => 'required|string',
                'type' => 'required|in:user,admin',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->authService->login(
                $request->national_id,
                $request->password,
                $request->type
            );

            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user' => new UserResource($result['user']),
                    'token' => $result['token'],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            $this->authService->logout($request->user());

            return response()->json([
                'success' => true,
                'message' => 'Logout successful',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user profile
     */
    public function profile(Request $request)
    {
        try {
            $user = $this->authService->profile($request->user());

            return response()->json([
                'success' => true,
                'message' => 'Profile retrieved successfully',
                'data' => new UserResource($user),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|max:255',
                'phone' => 'sometimes|string|max:255',
                'address' => 'sometimes|string|max:255',
                'summary' => 'sometimes|string',
                'link' => 'sometimes|url|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = $this->authService->updateProfile($request->user(), $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => new UserResource($user),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Refresh token
     */
    public function refreshToken(Request $request)
    {
        try {
            $result = $this->authService->refreshToken($request->user());

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'data' => [
                    'user' => new UserResource($result['user']),
                    'token' => $result['token'],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send OTP for password reset
     */
    public function forgetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'national_id' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $this->authService->forgetPassword($request->national_id);

            return response()->json([
                'success' => true,
                'message' => 'OTP sent to your email successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Reset password with OTP
     */
    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'national_id' => 'required|string',
                'otp' => 'required|string|size:6',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $this->authService->resetPassword(
                $request->national_id,
                $request->otp,
                $request->password
            );

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'national_id' => 'required|string',
                'otp' => 'required|string|size:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $result = $this->authService->verifyOtp($request->national_id, $request->otp);

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully',
                'data' => [
                    'user' => new UserResource($result['user']),
                    'token' => $result['token'],
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify OTP',
                'error' => $e->getMessage(),
            ], 400);
        }
    }
}
