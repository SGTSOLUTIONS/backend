<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function adminDetails(Request $request)
{
    $user = $request->user();

    if (!$user) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    return response()->json([
        'id'         => $user->id,
        'name'       => $user->name,
        'email'      => $user->email,
        'role'       => $user->role,
        'created_at' => $user->created_at,
    ]);
}

}
