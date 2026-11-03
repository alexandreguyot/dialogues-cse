<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

/**
 * Le mot de passe (1re ligne) et le secret TOTP (2e ligne) se lisent sur l'entrée standard
 * pour ne laisser de trace ni dans l'historique du shell ni dans la liste des processus.
 */
class CreateAdmin extends Command
{
    protected $signature = 'admin:create {email} {name}
        {--replace= : E-mail du compte existant à remplacer}
        {--stdin : Lire le mot de passe et le secret TOTP sur l\'entrée standard}
        {--send-reset : Envoyer un lien pour choisir le mot de passe}';

    protected $description = 'Crée un compte administrateur, ou remplace un compte existant';

    public function handle(): int
    {
        $email = Str::lower($this->argument('email'));
        $lines = $this->option('stdin') ? array_map('trim', file('php://stdin')) : [];

        $user = User::where('email', $this->option('replace') ?: $email)->first() ?? new User();

        $user->email    = $email;
        $user->name     = $this->argument('name');
        $user->password = ($lines[0] ?? '') ?: Str::password(40);

        if (! empty($lines[1])) {
            $user->two_factor_secret       = $lines[1];
            $user->two_factor_confirmed_at = now();
        }

        $user->save();
        $user->roles()->syncWithoutDetaching(Role::where('title', 'Admin')->pluck('id'));

        $this->info("Compte administrateur prêt : {$user->email}");

        if ($this->option('send-reset')) {
            $status = Password::sendResetLink(['email' => $user->email]);
            $this->info(__($status));

            return $status === Password::RESET_LINK_SENT ? self::SUCCESS : self::FAILURE;
        }

        return self::SUCCESS;
    }
}
