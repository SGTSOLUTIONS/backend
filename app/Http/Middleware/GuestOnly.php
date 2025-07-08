<?php

// app/Http/Middleware/GuestOnly.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            return response()->json(['message' => 'Already authenticated'], 403);
        }

        return $next($request);
    }
}
