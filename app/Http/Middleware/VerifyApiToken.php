<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class VerifyApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        $key = $request->header('secret-key');
        $user = User::where('api_token', $token)->where(['api_status' => 1, 'status' => 1])->first(); //tokens are unique there will be only one user with a token
        if ($user) {
            if ($user->secret_key == $key) {
                $request->attributes->add(['user' => $user]);
                return $next($request);
            }
            return response()->json(['errors' => ['authorization' => ['Invalid Secret_key!']], "messageType" => "error", 'status' => 0], 401);
        }
        return response()->json(['errors' => ['authorization' => ['Invalid Token!']], "messageType" => "error", 'status' => 0], 401);
    }
}
