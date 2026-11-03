<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Recopie la bibliothèque du dépôt "icones" dans resources/icones/.
 * Le paquet Composer n'accepte pas encore Laravel 13, et le choix dynamique d'une
 * icône par le client n'a pas besoin de ses composants Blade.
 */
class SyncIcones extends Command
{
    protected $signature = 'icones:sync {source=../icones : Dossier du dépôt icones}';

    protected $description = 'Met à jour la bibliothèque d\'icônes proposée dans l\'éditeur de pages';

    // Les icônes de basket n'ont pas d'usage sur ce site.
    private const EXCLUDED_THEMES = 'Basket';

    private const THEME_LABELS = ['Club et argent' => 'Vie du CSE'];

    public function handle(): int
    {
        $source = rtrim($this->argument('source'), '/');

        if (! File::exists("$source/outils/sources.php")) {
            $this->error("Dépôt icones introuvable dans « $source ».");

            return self::FAILURE;
        }

        $destination = resource_path('icones');
        File::deleteDirectory("$destination/svg");
        File::ensureDirectoryExists("$destination/svg");

        $catalogue = [];

        foreach (require "$source/outils/sources.php" as $theme => $icons) {
            if (str_starts_with($theme, self::EXCLUDED_THEMES)) {
                continue;
            }

            foreach (array_keys($icons) as $name) {
                $svg = File::get("$source/resources/svg/$name.svg");

                if (preg_match('/<script|<a\b|\son\w+=|href=/i', $svg)) {
                    $this->error("Icône refusée, contenu actif : $name");

                    return self::FAILURE;
                }

                File::put("$destination/svg/$name.svg", $svg);
                $catalogue[self::THEME_LABELS[$theme] ?? $theme][] = $name;
            }
        }

        File::put("$destination/catalogue.php", "<?php\n\n// Généré par `php artisan icones:sync`, ne pas modifier à la main.\n\nreturn " . var_export($catalogue, true) . ";\n");
        File::copy("$source/LICENCES-TIERCES.md", "$destination/LICENCES-TIERCES.md");

        $this->info(collect($catalogue)->flatten()->count() . ' icônes importées dans ' . count($catalogue) . ' thèmes.');

        return self::SUCCESS;
    }
}
