<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $socialiteUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Failed to authenticate with ' . ucfirst($provider) . '.');
        }

        // Find or create user
        $user = User::firstOrCreate(
            ['email' => $socialiteUser->getEmail()],
            [
                'name' => $socialiteUser->getName(),
                'email' => $socialiteUser->getEmail(),
                'email_verified_at' => now(),
                'password' => \Illuminate\Support\Str::random(32),
                'role' => 'renter',
            ]
        );

        // Update user's OAuth provider ID if provided
        $providerColumn = $provider . '_id';
        if (Schema::hasColumn('users', $providerColumn)) {
            $user->update([$providerColumn => $socialiteUser->getId()]);
        }

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
