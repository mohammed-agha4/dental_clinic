<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('dashboard.profile.edit', compact('user'));
    }


    public function update(Request $request)
    {

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthday' => 'nullable|date|before:today',
            'gender' => 'in:male,female',
            'profile_photo' => 'nullable|image'
        ]);

        $data = $request->except('profile_photo');


        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $path = $file->store('uploads', [
                'disk' => 'public'
            ]);

            $data['profile_photo'] = $path;
        }



        $user = $request->user();

        $user->profile->fill($data)->save();

        return redirect()->route('dashboard.profile.edit')->with('success', 'Profile Updated');
    }
}
