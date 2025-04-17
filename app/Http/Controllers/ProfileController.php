<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    // Get current user's profile
    public function index()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        return response()->json([
            'status' => 'success',
            'data' => $profile
        ], 200);
    }

    // Create or update user's profile
    public function update(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date',
            'marital_status' => 'required|max:20',
            'profession' => 'required|max:255',
            'address' => 'required|max:255',
            'phone' => 'required|max:15',
            'email' => 'required|email|max:255',
            'summary' => 'required',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $profile = Profile::firstOrNew();
                $profile->first_name = $validated['first_name'];
                $profile->last_name = $validated['last_name'];
                $profile->gender = $validated['gender'];
                $profile->dob = $validated['dob'];
                $profile->marital_status = $validated['marital_status'];
                $profile->profession = $validated['profession'];
                $profile->address = $validated['address'];
                $profile->phone = $validated['phone'];
                $profile->email = $validated['email'];
                $profile->summary = $validated['summary'];
                $profile->save();
            });

            return response()->json([
                'message' => 'Profile saved successfully!',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to save profile.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
