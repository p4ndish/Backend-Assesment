<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\BaseResponse;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @OA\Info(
 *     title="Job Finder API Documentation",
 *     version="1.0.0",
 *     description="API documentation for the Job Finder application",
 *     @OA\Contact(
 *         email="support@jobfinder.com"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     in="header",
 *     name="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * 
 * @OA\Tag(
 *     name="Authentication",
 *     description="Authentication endpoints"
 * )
 * 
 * @OA\Tag(
 *     name="User",
 *     description="User management endpoints"
 * )
 * 
 * @OA\Schema(
 *     schema="User",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="role", type="string", enum={"applicant", "company"}, example="applicant"),
 *     @OA\Property(property="email_verified_at", type="string", format="date-time", nullable=true, example=null),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T00:00:00.000000Z")
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Authentication"},
     *     summary="User login",
     *     description="Authenticate user and return JWT token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com", description="User email address"),
     *             @OA\Property(property="password", type="string", format="password", example="Password123!", description="User password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(
     *                 property="object",
     *                 type="object",
     *                 @OA\Property(property="user", ref="#/components/schemas/User"),
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *                 @OA\Property(property="token_type", type="string", example="bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=3600)
     *             ),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"), nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="object", type="object", nullable=true),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid credentials"),
     *             @OA\Property(property="object", type="object", nullable=true),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        try {
            // Find the user by email
            $user = User::where('email', $request->email)->first();

            // Check if user exists and password is correct
            if (!$user || !Hash::check($request->password, $user->password)) {
                return new BaseResponse(
                    null,
                    false,
                    'Invalid credentials.',
                    null,
                    ['Email or password is incorrect.']
                );
            }

            // Check if email is verified
            if (!$user->isEmailVerified()) {
                return new BaseResponse(
                    null,
                    false,
                    'Email not verified.',
                    null,
                    ['Please verify your email address before logging in.']
                );
            }

            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            // Get token expiration time
            $expiresIn = auth('api')->factory()->getTTL() * 60; // Convert to seconds

            return new BaseResponse(
                null,
                true,
                'Login successful.',
                [
                    'user' => (new UserResource($user))->resolve(),
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => $expiresIn,
                ]
            );

        } catch (\Exception $e) {
            return new BaseResponse(
                null,
                false,
                'Login failed. Please try again.',
                null,
                ['An error occurred during login.']
            );
        }
    }

    public function forgotPasswordGet(Request $request)
    {
        // TODO: Implement forgotPasswordGet logic
        return response()->json(['message' => 'forgotPasswordGet method not implemented yet']);
    }

    public function verifyOtp(Request $request)
    {
        // TODO: Implement verifyOtp logic
        return response()->json(['message' => 'verifyOtp method not implemented yet']);
    }

    public function sendOtp(Request $request)
    {
        // TODO: Implement sendOtp logic
        return response()->json(['message' => 'sendOtp method not implemented yet']);
    }

    public function resendOTP(Request $request)
    {
        // TODO: Implement resendOTP logic
        return response()->json(['message' => 'resendOTP method not implemented yet']);
    }

    public function resetPassword(Request $request)
    {
        // TODO: Implement resetPassword logic
        return response()->json(['message' => 'resetPassword method not implemented yet']);
    }

    public function refresh(Request $request)
    {
        // TODO: Implement refresh logic
        return response()->json(['message' => 'refresh method not implemented yet']);
    }

    public function logout(Request $request)
    {
        // TODO: Implement logout logic
        return response()->json(['message' => 'logout method not implemented yet']);
    }

    public function me(Request $request)
    {
        // TODO: Implement me logic
        return response()->json(['message' => 'me method not implemented yet']);
    }

    public function updateUser(Request $request)
    {
        // TODO: Implement updateUser logic
        return response()->json(['message' => 'updateUser method not implemented yet']);
    }

    public function updatePassword(Request $request)
    {
        // TODO: Implement updatePassword logic
        return response()->json(['message' => 'updatePassword method not implemented yet']);
    }
}
