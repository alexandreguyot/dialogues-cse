<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

/** La bibliothèque d'icônes de resources/icones, mise à jour par `php artisan icones:sync`. */
class PageIcons
{
    private static ?array $catalogue = null;

    /** @return array<string, string> nom de l'icône => thème */
    public static function names(): array
    {
        if (self::$catalogue === null) {
            self::$catalogue = [];

            foreach (require resource_path('icones/catalogue.php') as $theme => $names) {
                self::$catalogue += array_fill_keys($names, $theme);
            }
        }

        return self::$catalogue;
    }

    /** Le nom est vérifié contre le catalogue : il sert à construire un chemin de fichier. */
    public static function svg(?string $name, string $class = 'icone'): ?string
    {
        if (blank($name) || ! array_key_exists($name, self::names())) {
            return null;
        }

        return str_replace('<svg ', '<svg class="' . e($class) . '" aria-hidden="true" ', File::get(resource_path("icones/svg/$name.svg")));
    }

    /** Les données du sélecteur d'icônes de l'éditeur. */
    public static function library(): array
    {
        return collect(self::names())->map(fn ($theme, $name) => [
            'value' => $name,
            'label' => str_replace('-', ' ', $name),
            'theme' => $theme,
            'svg'   => self::svg($name),
        ])->values()->all();
    }
}
