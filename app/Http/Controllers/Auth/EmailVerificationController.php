<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResponse;
use App\Mail\EmailVerificationMail;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * @OA\Tag(
 *     name="Email Verification",
 *     description="Email verification endpoints"
 * )
 */
class EmailVerificationController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/auth/verify-email",
     *     tags={"Email Verification"},
     *     summary="Verify email with token",
     *     description="Verify user email using verification token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="abc123...", description="Email verification token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Email verified successfully! You can now log in to your account."),
     *             @OA\Property(property="object", type="object", nullable=true),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"), nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid or expired token",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid verification token."),
     *             @OA\Property(property="object", type="object", nullable=true),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     * 
     * @OA\Get(
     *     path="/api/auth/verify-email",
     *     tags={"Email Verification"},
     *     summary="Verify email with token (GET)",
     *     description="Verify user email using verification token from URL query parameter",
     *     @OA\Parameter(
     *         name="token",
     *         in="query",
     *         required=true,
     *         description="Email verification token",
     *         @OA\Schema(type="string", example="abc123...")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Email verified successfully! You can now log in to your account."),
     *             @OA\Property(property="object", type="object", nullable=true),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"), nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid or expired token",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid verification token."),
     *             @OA\Property(property="object", type="object", nullable=true),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    public function verify(Request $request)
    {
        // Handle both GET and POST requests
        $token = $request->input('token') ?? $request->query('token');

        if (!$token) {
            return new BaseResponse(
                null,
                false,
                'Token is required.',
                null,
                ['Please provide a verification token.']
            );
        }

        // Find the verification record
        $verification = EmailVerification::where('token', $token)->first();

        if (!$verification) {
            return new BaseResponse(
                null,
                false,
                'Invalid verification token.',
                null,
                ['The verification token is invalid or malformed.']
            );
        }

        // Get the user
        $user = $verification->user;

        if (!$user) {
            return new BaseResponse(
                null,
                false,
                'User not found.',
                null,
                ['The user associated with this token was not found.']
            );
        }

        // Check if email is already verified
        if ($user->isEmailVerified()) {
            return new BaseResponse(
                null,
                true,
                'Email is already verified. No further action is required.',
                null
            );
        }

        // Check if token is already used
        if ($verification->is_used) {
            return new BaseResponse(
                null,
                false,
                'Token already used.',
                null,
                ['This verification token has already been used.']
            );
        }

        // Check if token is expired
        if ($verification->isExpired()) {
            try {
                DB::beginTransaction();

                // Delete the expired token
                $verification->delete();

                // Generate new verification token
                $newToken = Str::random(64);
                $expiresAt = now()->addHour();

                // Create new email verification record
                EmailVerification::create([
                    'user_id' => $user->id,
                    'token' => $newToken,
                    'expires_at' => $expiresAt,
                ]);

                // Send new verification email
                $verificationUrl = config('app.url') . '/api/auth/verify-email?token=' . $newToken;
                
                Mail::to($user->email)->send(new EmailVerificationMail($user, $verificationUrl));

                DB::commit();

                return new BaseResponse(
                    null,
                    false,
                    'Verification token has expired. A new verification email has been sent to your email address.',
                    null,
                    ['Please check your email for the new verification link.']
                );

            } catch (\Exception $e) {
                DB::rollBack();
                
                return new BaseResponse(
                    null,
                    false,
                    'Failed to send new verification email. Please try again.',
                    null,
                    ['An error occurred while processing your request.']
                );
            }
        }

        // Token is valid, proceed with verification
        try {
            DB::beginTransaction();

            // Mark email as verified
            $user->update([
                'email_verified_at' => now(),
            ]);

            // Mark verification token as used
            $verification->update([
                'is_used' => true,
            ]);

            DB::commit();

            return new BaseResponse(
                null,
                true,
                'Email verified successfully! You can now log in to your account.',
                null
            );

        } catch (\Exception $e) {
            DB::rollBack();
            
            return new BaseResponse(
                null,
                false,
                'Email verification failed. Please try again.',
                null,
                ['An error occurred during email verification.']
            );
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/check-verification",
     *     tags={"Email Verification"},
     *     summary="Check email verification status",
     *     description="Check if a user's email is verified",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com", description="User email address")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Verification status retrieved",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Verification status retrieved successfully."),
     *             @OA\Property(
     *                 property="object",
     *                 type="object",
     *                 @OA\Property(property="is_verified", type="boolean", example=true),
     *                 @OA\Property(property="email", type="string", example="john@example.com")
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
     *     )
     * )
     */
    public function checkVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        return new BaseResponse(
            null,
            true,
            'Verification status retrieved successfully.',
            [
                'is_verified' => $user->isEmailVerified(),
                'email' => $user->email,
            ]
        );
    }
}
