<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => ['required', 'image', 'max:2048'], // 2MB Max
        ]);

        $user = auth()->user();

        // Delete old photo if it exists
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Store new photo
        $path = $request->file('profile_photo')->store('profile-photos', 'public');

        // Update user record
        $user->update([
            'profile_photo_path' => $path,
        ]);

        return back()->with('success', 'Profile photo updated successfully!');
    }
}
