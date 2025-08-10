<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\BaseResponse;
use App\Http\Resources\UserResource;
use App\Mail\EmailVerificationMail;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="Registration",
 *     description="User registration endpoints"
 * )
 */
class RegisterController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     tags={"Registration"},
     *     summary="Register a new user",
     *     description="Register a new user with email verification",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe", description="Full name with one space between first and last name"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com", description="Valid email address"),
     *             @OA\Property(property="password", type="string", format="password", example="Password123!", description="Strong password with uppercase, lowercase, number and special character"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="Password123!", description="Password confirmation"),
     *             @OA\Property(property="role", type="string", enum={"applicant", "company"}, example="applicant", description="User role")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Registration successful! Please check your email to verify your account."),
     *             @OA\Property(property="object", type="object", ref="#/components/schemas/User"),
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
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            // Generate verification token
            $token = Str::random(64);
            $expiresAt = now()->addHour(); // Token expires in 1 hour

            // Create email verification record
            EmailVerification::create([
                'user_id' => $user->id,
                'token' => $token,
                'expires_at' => $expiresAt,
            ]);

            // Send verification email
            $verificationUrl = config('app.url') . '/api/auth/verify-email?token=' . $token;
            
            Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationUrl));

            DB::commit();

            return new BaseResponse(
                null,
                true,
                'Registration successful! Please check your email to verify your account.',
                (new UserResource($user))->resolve()
            );

        } catch (\Exception $e) {
            DB::rollBack();
            
            return new BaseResponse(
                null,
                false,
                'Registration failed. Please try again.',
                null,
                ['An error occurred during registration.']
            );
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/resend-verification",
     *     tags={"Registration"},
     *     summary="Resend verification email",
     *     description="Resend email verification link to user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com", description="User email address")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Verification email sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Verification email sent successfully!"),
     *             @OA\Property(property="object", type="object", nullable=true),
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
     *     )
     * )
     */
    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->isEmailVerified()) {
            return new BaseResponse(
                null,
                false,
                'Email is already verified.',
                null,
                ['Email verification is not required.']
            );
        }

        try {
            // Delete any existing verification tokens for this user
            EmailVerification::where('user_id', $user->id)->delete();

            // Generate new verification token
            $token = Str::random(64);
            $expiresAt = now()->addHour();

            // Create new email verification record
            EmailVerification::create([
                'user_id' => $user->id,
                'token' => $token,
                'expires_at' => $expiresAt,
            ]);

            // Send verification email
            $verificationUrl = config('app.url') . '/api/auth/verify-email?token=' . $token;
            
            Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationUrl));

            return new BaseResponse(
                null,
                true,
                'Verification email sent successfully!',
                null
            );

        } catch (\Exception $e) {
            return new BaseResponse(
                null,
                false,
                'Failed to send verification email. Please try again.',
                null,
                ['An error occurred while sending the verification email.']
            );
        }
    }
}
