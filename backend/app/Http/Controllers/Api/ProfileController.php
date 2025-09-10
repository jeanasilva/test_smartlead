<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Task;

class ProfileController extends Controller
{
    /**
     * Get user profile with stats
     */
    public function show()
    {
        $user = Auth::user()->load('company');

        $stats = [
            'total_tasks' => Task::where('user_id', $user->id)->count(),
            'completed_tasks' => Task::where('user_id', $user->id)->where('status', 'completed')->count(),
            'in_progress_tasks' => Task::where('user_id', $user->id)->where('status', 'in_progress')->count(),
            'pending_tasks' => Task::where('user_id', $user->id)->where('status', 'pending')->count(),
        ];

        return response()->json([
            'user' => $user,
            'stats' => $stats
        ]);
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only(['name', 'email']));

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->load('company')
        ]);
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
                'errors' => [
                    'current_password' => ['A senha atual está incorreta']
                ]
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * Get user stats only
     */
    public function stats()
    {
        $user = Auth::user();

        $stats = [
            'total_tasks' => Task::where('user_id', $user->id)->count(),
            'completed_tasks' => Task::where('user_id', $user->id)->where('status', 'completed')->count(),
            'in_progress_tasks' => Task::where('user_id', $user->id)->where('status', 'in_progress')->count(),
            'pending_tasks' => Task::where('user_id', $user->id)->where('status', 'pending')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Delete user account
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        // Soft delete or hard delete based on your requirements
        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully'
        ]);
    }
}
