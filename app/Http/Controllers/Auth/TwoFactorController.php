<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Support\TwoFactor;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function setup(Request $request)
    {
        $user = $request->user();

        if ($user->hasTwoFactor()) {
            return redirect(RouteServiceProvider::HOME);
        }

        $secret = session('two_factor_pending_secret') ?? TwoFactor::generateSecret();
        session(['two_factor_pending_secret' => $secret]);

        return view('auth.two-factor.setup', [
            'secret' => $secret,
            'qrCode' => TwoFactor::qrCode($user, $secret),
        ]);
    }

    public function confirm(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $user   = $request->user();
        $secret = session('two_factor_pending_secret');

        if ($user->hasTwoFactor() || ! $secret) {
            return redirect()->route('two-factor.setup');
        }

        if (! TwoFactor::verify($secret, $request->input('code'), $user->id)) {
            return back()->withErrors(['code' => 'Ce code ne correspond pas. Saisissez le code affiché en ce moment dans l\'application.']);
        }

        [$codes, $hashes] = TwoFactor::generateRecoveryCodes();

        $user->two_factor_secret         = $secret;
        $user->two_factor_recovery_codes = $hashes;
        $user->two_factor_confirmed_at   = now();
        $user->save();

        session()->forget('two_factor_pending_secret');
        TwoFactor::markPassed($user);

        return redirect()->route('two-factor.recovery')->with('recovery_codes', $codes);
    }

    public function recovery()
    {
        if (! session('recovery_codes')) {
            return redirect(RouteServiceProvider::HOME);
        }

        return view('auth.two-factor.recovery', ['codes' => session('recovery_codes')]);
    }

    public function challenge(Request $request)
    {
        $user = $request->user();

        if (! $user->hasTwoFactor()) {
            return redirect()->route('two-factor.setup');
        }

        if (TwoFactor::passed($user)) {
            return redirect(RouteServiceProvider::HOME);
        }

        return view('auth.two-factor.challenge');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $user = $request->user();
        $code = $request->input('code');

        if (! $user->hasTwoFactor()) {
            return redirect()->route('two-factor.setup');
        }

        if (! TwoFactor::verify($user->two_factor_secret, $code, $user->id) && ! TwoFactor::useRecoveryCode($user, $code)) {
            return back()->withErrors(['code' => 'Ce code n\'est pas valide. Saisissez le code affiché en ce moment dans l\'application.']);
        }

        TwoFactor::markPassed($user);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /** Réservé aux comptes non administrateurs : l'authenticator est obligatoire pour un admin. */
    public function disable(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $user = $request->user();

        abort_if($user->is_admin, 403);

        if (! $user->hasTwoFactor() || ! TwoFactor::verify($user->two_factor_secret, $request->input('code'), $user->id)) {
            return back()->withErrors(['code' => 'Ce code n\'est pas valide.']);
        }

        $user->two_factor_secret         = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at   = null;
        $user->save();

        return redirect()->route('profile.show')->with('status', 'Authenticator désactivé.');
    }
}
