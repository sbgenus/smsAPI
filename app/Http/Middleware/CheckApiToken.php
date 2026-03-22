<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\ApiToken;
use App\Models\User;

class CheckApiToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');
        if (!$token || !str_starts_with($token, 'Bearer ')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $token = substr($token, 7);
        $apiToken = ApiToken::where('token', $token)->first();
        if (!$apiToken) {
            return response()->json(['error' => 'Invalid or expired token'], 401);
        }
        $user = User::find($apiToken->tokenable_id);
        $request->merge(['auth_user_id' => $apiToken->tokenable_id, 'user' => $user]);
        return $next($request);
    }
}
