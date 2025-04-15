<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\OtpVerificationEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class OtpsendController extends Controller
{
    /**
     * Generate OTP and send it to the user's email.
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid email address.',
                'errors' => $validator->errors()
            ], 400);
        }

        // Optional: block already registered emails
        if (\App\Models\User::where('email', $request->email)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email already registered. Please login.'
            ], 400);
        }

        // Generate OTP
        $otp = rand(100000, 999999);

        // Send OTP
        try {
            Mail::to($request->email)->send(new OtpVerificationEmail($otp));
            return response()->json([
                'status' => 'success',
                'message' => 'OTP sent successfully to ' . $request->email,
                'otp' => $otp // for testing only
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send OTP: ' . $e->getMessage()
            ], 500);
        }
    }
}
