<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        $google = config('services.google', []);

        if (empty($google['client_id']) || empty($google['client_secret']) || empty($google['redirect'])) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Google sign-in is not configured. Add GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, and GOOGLE_REDIRECT_URI to .env, then run php artisan config:clear.',
                );
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException) {
            return redirect()
                ->route('login')
                ->with('error', 'Google sign-in expired. Please try again.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('login')
                ->with('error', 'Google sign-in failed. Please try again.');
        }

        $user = User::query()->where('google_id', $googleUser->getId())->first();

        if ($user === null) {
            $user = User::query()->where('email', $googleUser->getEmail())->first();

            if ($user !== null) {
                $user->forceFill([
                    'google_id' => $googleUser->getId(),
                    'name' => $googleUser->getName() ?: $user->name,
                ])->save();
            } else {
                $user = User::query()->create([
                    'google_id' => $googleUser->getId(),
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                ]);
            }
        } else {
            $user->forceFill([
                'name' => $googleUser->getName() ?: $user->name,
                'email' => $googleUser->getEmail(),
            ])->save();
        }

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
