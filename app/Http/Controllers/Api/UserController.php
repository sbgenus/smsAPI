<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
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
}
