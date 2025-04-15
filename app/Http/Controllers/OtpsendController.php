<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\OtpVerificationEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Models\Otp;
use Carbon\Carbon;

class OtpsendController extends Controller
{
    /**
     * Generate OTP and send it to the user's email.
     */

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $otp = rand(100000, 999999);

        // Delete any previous OTPs for this email
        Otp::where('email', $request->email)->delete();

        // Save OTP to database with expiry (e.g. 10 mins from now)
        Otp::create([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(10), // ⬅️ Set expiry
        ]);

        // Send OTP via email
        Mail::to($request->email)->send(new OtpVerificationEmail($otp));


        return response()->json(['status' => 'success', 'message' => 'OTP sent successfully.']);
    }
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6'
        ]);

        $otpRecord = Otp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if ($otpRecord) {
            // Delete OTP after successful verification
            $otpRecord->delete();

            // Mark user as verified (optional), or return success
            return response()->json([
                'status' => 'success',
                'message' => 'OTP verified successfully.'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid or expired OTP.'
        ], 400);
    }
}
