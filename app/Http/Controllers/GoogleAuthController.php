<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WeddingDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes([
                'openid',
                'profile',
                'email',
                'https://www.googleapis.com/auth/calendar.events',
            ])
            ->with([
                'access_type' => 'offline',
                'prompt' => 'consent select_account',
            ])
            ->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('email', $googleUser->getEmail())->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName() ?: 'Google User',
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(Str::random(32)),
            ]);
        }

        $refreshToken = $googleUser->refreshToken ?: $user->google_refresh_token;

        $user->forceFill([
            'google_id' => $googleUser->getId(),
            'google_token' => $googleUser->token,
            'google_refresh_token' => $refreshToken,
            'google_token_expires_at' => now()->addSeconds($googleUser->expiresIn ?? 3600),
            'google_calendar_connected_at' => now(),
        ])->save();

        Auth::login($user);

        request()->session()->regenerate();

        $hasWeddingDetails = WeddingDetail::where('user_id', $user->id)->exists();

        if (! $hasWeddingDetails) {
            return redirect()->route('wedding.setup');
        }

        return redirect()->route('dashboard');
    }
}
