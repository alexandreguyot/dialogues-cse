<?php

namespace App\Support;

use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

/** Double authentification par application (TOTP), vérifiée à chaque nouvelle session. */
class TwoFactor
{
    private const SESSION_KEY = 'two_factor_passed_for';

    /** Faux tant que l'utilisateur doit encore activer ou saisir son code : AuthGates ne lui accorde alors aucun droit. */
    public static function satisfied(User $user): bool
    {
        return $user->hasTwoFactor() ? self::passed($user) : ! $user->is_admin;
    }

    public static function passed(User $user): bool
    {
        return session(self::SESSION_KEY) === $user->id;
    }

    public static function markPassed(User $user): void
    {
        session()->regenerate();
        session([self::SESSION_KEY => $user->id]);
    }

    public static function generateSecret(): string
    {
        return (new Google2FA())->generateSecretKey(32);
    }

    public static function verify(string $secret, string $code, int $userId): bool
    {
        $code = preg_replace('/\s+/', '', $code);

        // Un code déjà utilisé ne peut pas resservir pendant sa durée de validité.
        return (new Google2FA())->verifyKey($secret, $code, 1)
            && Cache::add("two-factor:$userId:$code", true, 120);
    }

    public static function qrCode(User $user, string $secret): string
    {
        $url = (new Google2FA())->getQRCodeUrl(config('app.name'), $user->email, $secret);

        return (new Writer(new ImageRenderer(new RendererStyle(220, 1), new SvgImageBackEnd())))->writeString($url);
    }

    /** @return array{0: list<string>, 1: list<string>} les codes en clair à afficher une fois, et leurs empreintes */
    public static function generateRecoveryCodes(): array
    {
        $codes = collect(range(1, 8))->map(fn () => Str::lower(Str::random(5) . '-' . Str::random(5)));

        return [$codes->all(), $codes->map(fn ($code) => Hash::make($code))->all()];
    }

    public static function useRecoveryCode(User $user, string $code): bool
    {
        $codes = $user->two_factor_recovery_codes ?? [];

        foreach ($codes as $index => $hash) {
            if (Hash::check(trim(Str::lower($code)), $hash)) {
                unset($codes[$index]);
                $user->two_factor_recovery_codes = array_values($codes);
                $user->save();

                return true;
            }
        }

        return false;
    }
}
