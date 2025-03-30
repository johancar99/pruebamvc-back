<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CheckTokenExpiration
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->user()->currentAccessToken();

        if ($token && $token->expires_at && Carbon::now()->greaterThan($token->expires_at)) {
            $token->delete();
            return response()->json(['error' => 'El token ha expirado'], 401);
        }

        return $next($request);
    }
}

