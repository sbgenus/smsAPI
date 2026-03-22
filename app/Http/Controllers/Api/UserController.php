<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Create User
    public function store(Request $request) {
        // Validation
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Create User
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('api_token')->plainTextToken;
        $tokenData = ApiToken::where('tokenable_id', $user->id)->first();
        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'data' => $user,
            'token' => $tokenData->token
        ], 201);
    }
    // Get User Data
    public function getUserData(Request $request) {
        $user = $request->user;
        return response()->json([
            'status' => 'success',
            'message' => 'User data retrieved from user Controller successfully',
            'user' => $user
        ]);
    }
    // get user ballance
    public function getUserBallance(Request $request) {
        $user = $request->user;
        $ballance = $user->credits;
        return response()->json([
            'status' => 'success',
            'message' => 'User ballance retrieved successfully',
            'ballance' => $ballance
        ]);
    }
    // Add User  Ballance
    public function addUserBallance(Request $request) {
        $uset = $request->user;
        if(!$uset->id){
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'newBall'     => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        $uset->credits += $request->newBall;
        $uset->save();
        return response()->json([
            'status' => 'success',
            'message' => 'User ballance added successfully',
            'ballance' => $uset->credits
        ]);
    }
}
