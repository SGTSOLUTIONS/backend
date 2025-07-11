<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => $this->formatUserData($user)
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:users,email,'.$user->id,
            'password' => ['sometimes', 'nullable', Password::defaults()],
            'role' => 'sometimes|required|string|in:user,admin,manager',
            'phone' => 'sometimes|nullable|string|max:20',
            'address' => 'sometimes|nullable|string|max:500',
            'designation' => 'sometimes|nullable|string|max:255',
            'dob' => 'sometimes|nullable|date',
            'gender' => 'sometimes|nullable|string|in:male,female,other,prefer-not-to-say',
            'avatar' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];

        $validated = $request->validate($rules);

        // Handle file upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars');
            $validated['avatar'] = $path;
        }

        // Hash password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'data' => $this->formatUserData($user->fresh()),
            'message' => 'Profile updated successfully'
        ]);
    }

    public function destroy()
    {
        $user = Auth::user();

        // Delete avatar if exists
        if ($user->avatar) {
            Storage::delete($user->avatar);
        }

        // Logout before deleting
        Auth::logout();

        // Delete user
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully'
        ]);
    }

    private function formatUserData(User $user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'phone' => $user->phone,
            'avatar' => $user->avatar ? Storage::url($user->avatar) : null,
            'address' => $user->address,
            'designation' => $user->designation,
            'dob' => $user->dob,
            'gender' => $user->gender,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }
}
