<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetTwoFactor extends Command
{
    protected $signature = 'two-factor:reset {email}';

    protected $description = 'Retire l\'authenticator d\'un compte qui a perdu son téléphone et ses codes de secours';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('Compte introuvable.');

            return self::FAILURE;
        }

        $user->two_factor_secret         = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at   = null;
        $user->save();

        $this->info('Authenticator retiré. Il sera redemandé à la prochaine connexion si le compte est administrateur.');

        return self::SUCCESS;
    }
}
