<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Otp;

use App\Services\SMSService;

class AuthenticationController extends Controller
{
    public function __construct(protected SMSService $smsService){
        $this->smsService = $smsService;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|digits:10|regex:/^[6789]/',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }else{
            
            $user = User::where('phone', $request->phone_number)->first();

            // Check if the user exists
            if ($user) {
                if($user->role == 'user' && $user->status == 1){
                    if ($request->otp) {
                        return $this->verifyOTP($request);
                    } else {
                        return $this->sendNewOTP($user->phone);
                    }
                }else{
                    return response()->json(['status' => 'false','message' => 'This user not exists in this role or Not Active.'], 401);
                }
            } else {
                // New user, need to register first or check OTP
                if ($request->otp) {
                    return response()->json(['status' => 'false','message' => 'Unauthorized: Please register your account.'], 401);
                } else {
                    return $this->register($request->phone_number);
                }
            }
        }
    }

    protected function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|digits:10|regex:/^[6789]/',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::where('phone', $request->phone_number)->first();

        if (!$user) {
            return response()->json(['status' => 'false','message' => 'Please register your account.'], 401);
        }

        // If User exists then verify OTP
        $otp = Otp::where('user_id', $user->id)->latest()->first();

        if(!$otp || $otp->otp != $request->otp){
            return response()->json(['status' => 'false','message' => 'Invalid OTP'], 401);
        }

        // Check if token matches
        $token = $token = $user->createToken('Personal Access Token')->plainTextToken;
        if (!$token) {
            return response()->json(['status' => 'false','message' => 'Token not found'], 401);
        }

        // Token is valid, proceed with login
        $otpCreatedAt = Carbon::parse($otp->created_at);
        $currentTime = Carbon::now(); 
        $otpValidityDuration = 5; // OTP valid for 5 minute

        if ($otpCreatedAt->diffInMinutes($currentTime) > $otpValidityDuration) {
            return response()->json(['status' => 'false','message' => 'OTP has expired.'], 401);
        }

        $user->image = $user->getFirstMediaUrl('user-image');
        return response()->json([
            'status' => 'true',
            'message' => 'Login successful', 
            'token' => $token,
            'user'=>$user
        ]);
    }

    protected function register($phoneNumber)
    {
        // Check if user with this phone number already exists
        $existingUser = User::where('phone', $phoneNumber)->first();
        if ($existingUser) {
            return response()->json(['status' => 'false','message' => 'User already exists.'], 400);
        }
    
        // Generate OTP
        $user = User::create(['phone' => $phoneNumber,'role' => 'user','status' => 1]);

        if($phoneNumber == '8967464432'){
            $otp = 1234;
        }else{
            // $otp = generateOTP();
            $otp = 1234;
        }

        Otp::create(['user_id' => $user->id, 'otp' => $otp, 'created_at' => now()]);
        // $this->smsService->sendSMS('91'.$phoneNumber,$otp);

        return response()->json([
            'status' => 'true',
            'message' => 'Your account has been created.',
            'sent' => 'OTP sent to your phone number.',
            'note' => 'OTP is valid for 5 minute.',
            // 'token' => $token,
        ]);
    }
    

    protected function sendNewOTP($phoneNumber)
    {
        if($phoneNumber == '8967464432'){
            $otp = 1234;
        }else{
            // $otp = generateOTP();
            $otp = 1234;
        }
        $user = User::where('phone', $phoneNumber)->first();
        Otp::where('user_id', $user->id)->update(['otp' => $otp, 'created_at' => now()]);
        // $this->smsService->sendSMS('91'.$phoneNumber,$otp);
    
        return response()->json([
            'status' => 'true',
            'message' => 'New OTP generated successfully.',
            'sent' => 'An OTP has been sent to your phone number.',
            'note' => 'OTP is valid for 1 minute.',
            // 'token' => $token, // Send updated token in the response
        ]);
    }

    public function update_profile(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $request->user()->id,
            'gender' => 'required|string|in:male,female,others',
            'address' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::find($request->user()->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->gender = $request->gender;
        $user->address = $request->address;
        $user->latitude = $request->latitude;
        $user->longitude = $request->longitude;

        // Update profile image if provided
        if ($request->has('image') && !empty($request->input('image'))) {
            $base64Image = $request->input('image');
            $user->clearMediaCollection('user-image');
            $user->addMediaFromBase64($base64Image)
            ->usingFileName(now()->format('Y-m-d_H-i-s') . '.png')
            ->toMediaCollection('user-image');
        }        

        $res = $user->update();
        $user->getFirstMediaUrl('user-image');

        if ($res) {
            return response()->json([
                'status' => 'true',
                'message' => 'Profile updated successfully.',
                'data' =>  $user,
            ]);
        } else {
            return response()->json([
                'status' => 'false',
                'message' => 'Failed to update profile.',
            ]);
        }
    }


    public function get_user_data(Request $request){
        return $request->user();
    }
}
