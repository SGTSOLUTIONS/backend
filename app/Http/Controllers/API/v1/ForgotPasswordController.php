<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Validator; // Add this import
use Illuminate\Support\Facades\Hash;     // Add this for password hashing
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function sendResetLink(Request $request)
    {
        // 1. Validate email
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // 2. Generate 6-digit OTP
        $otp = random_int(100000, 999999);

        // 3. Store OTP in password_resets table
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => $otp, // use token column to store OTP
                'created_at' => Carbon::now()
            ]
        );

        // 4. Send OTP via email
        \Mail::raw("Your OTP to reset the password is: $otp", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Password Reset OTP');
        });

        return response()->json(['message' => 'OTP sent to your email.','success'=>true]);
    }
    public function verifyOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'otp' => 'required|numeric',
    ]);

    $record = DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->where('token', $request->otp)
        ->first();

    if (!$record) {
        return response()->json(['success' => false, 'message' => 'Invalid OTP'], 422);
    }

    // Optionally check expiration (e.g., 10 mins)
    if (Carbon::parse($record->created_at)->addMinutes(10)->isPast()) {
        return response()->json(['success' => false, 'message' => 'OTP expired'], 422);
    }

    return response()->json(['success' => true]);
}
public function resetPassword(Request $request)
    {
        // 1. Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',

            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Check if OTP exists and is valid
        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)

            ->first();

        if (!$reset) {
            return response()->json(['message' => 'Invalid or expired OTP'], 422);
        }

        // Optional: Check if OTP is older than 10 minutes
        if (Carbon::parse($reset->created_at)->addMinutes(10)->isPast()) {
            return response()->json(['message' => 'OTP expired'], 422);
        }

        // 3. Update user's password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // 4. Remove OTP record
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password has been reset successfully']);
    }
}
