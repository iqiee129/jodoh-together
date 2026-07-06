<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminSettingsController extends Controller
{
    public function edit()
    {
        return view('admin.settings');
    }

    public function updateProfile(Request $request)
    {
        $admin = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'regex:/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/',
                'max:255',
                Rule::unique('users', 'email')->ignore($admin->id),
            ],
        ], [
            'email.regex' => 'Please enter a valid email address, like name@example.com.',
        ]);

        $admin->update($data);

        return redirect()
            ->route('admin.settings')
            ->with('profile_success', 'Admin profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $admin = $request->user();

        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($request->current_password, $admin->password)) {
            return back()
                ->withErrors(['current_password' => 'Current password is incorrect.'])
                ->withInput();
        }

        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('admin.settings')
            ->with('password_success', 'Password updated successfully.');
    }

    public function updateTheme(Request $request)
    {
        $admin = $request->user();

        $data = $request->validate([
            'theme_mode' => ['required', 'in:light,dark'],
        ]);

        $admin->update($data);

        return redirect()
            ->route('admin.settings')
            ->with('theme_success', 'Theme updated successfully.');
    }
}
