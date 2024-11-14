<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Validator;
use Hash;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    public function register(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|max:12',
        ]);

        if ($validator->fails()) {
            $msg = $validator->errors()->first();
            return response()->json([ 'status' => false, 'message' => $msg], 400);
        }

        $data = $validator->validated();
        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Registration successful',
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], 201);
    }

    public function login(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8|max:12',
        ]);

        if ($validator->fails()) {
            $msg = $validator->errors()->first();
            return response()->json([ 'status' => false, 'message' => $msg], 400);
        }

        $credentials = $validator->validated();

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('my_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Login Successfully',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid email or password',
            ], 401);
        }
    }

    public function updateUser(Request $req){

        $tableColumns = Schema::getColumnListing('users');

        $data = $req->all();

        $filteredData = array_filter($data, function($key) use ($tableColumns) {
            return in_array($key, $tableColumns) && $key !== 'profileImage';
        }, ARRAY_FILTER_USE_KEY);

        if (isset($data['profileImage'])) {
            $filteredData['profileImage'] = $this->handleImageUpload($data['profileImage'], 'profile_Image');
        }

        $userId = Auth::user()->id;

        $qrCode = User::where('id', $userId)->update($filteredData);

        return response()->json([
            'message' => 'User Updated Successfully',
            'status' => true
        ], 200);
    }
    public function handleImageUpload($image, $type)
    {
        // Generate a unique filename
        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
        // Define the path
        $path = public_path('images/' . $type . '/' . $filename);
        // Save the image to the public directory
        $image->move(public_path('images/' . $type), $filename);
        return 'images/' . $type . '/' . $filename;
    }
    public function getUser()
    {
        $user = Auth::user();
        // dd($user);
        // foreach ($user as $user) {
             $user->profileImage = isset($user->profileImage) ? url($user->profileImage) : '';
        // }
        return response()->json([
            'status' => true,
            'message' => "Success",
            'user' => $user,
        ]);
    }
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            $msg = $validator->errors()->first();
            return response()->json([ 'status' => false, 'message' => $msg], 400);
        }

        $otpCode = rand(100000, 999999);

        $user = User::where('email', $request->email)->first();
        $user->update(['otp' => $otpCode]);

        $data = [
            'otp' => $otpCode,
        ];
        $companyName = "QR Code";

        Mail::send('forgot_pass_mail', ['otp' => $otpCode, 'companyName'=>$companyName], function ($message) use ($user, $companyName) {
            $message->to($user->email, $user->name)
                    ->subject('Password Recovery Mail from ' . $companyName);
            $message->from('wasifbaloch527@gmail.com', $companyName);
        });

        return response()->json(['message' => 'OTP sent to email'], 200);
    }
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|numeric',
            'newPassword' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            $msg = $validator->errors()->first();
            return response()->json([ 'status' => false, 'message' => $msg], 400);
        }

        $user = User::where('email', $request->email)->where('otp', $request->otp)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        $user->update([
            'password' => Hash::make($request->newPassword),
            'otp' => null
        ]);

        return response()->json(['message' => 'Password reset successful'], 200);
    }

        public function logout(){
            $user = Auth::user();
                $user->tokens()
                ->where('id', $user->currentAccessToken()->id)
                ->delete();

            return response()->json( [
                'message' => 'Logged Out Successfully',
                'status' => 0
            ] );

        }


}
