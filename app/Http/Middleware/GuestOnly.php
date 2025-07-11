<?php

// app/Http/Middleware/GuestOnly.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('sanctum')->check()) {
            return response()->json(['message' => 'Already authenticated'], 403);
        }

        return $next($request);
    }
}

