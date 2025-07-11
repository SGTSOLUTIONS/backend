<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Enums\UserRole;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
if ($user ){

        return $next($request);
  }
        if (!$user || !in_array($user->role, $roles)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
  if ($user ){

        return $next($request);
  }
    }
}
