<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Catalogue des blocs utilisables dans une page. L'éditeur et la validation sont générés
 * à partir de cette définition, le rendu est dans resources/views/site/blocks/{type}.blade.php.
 */
class PageBlocks
{
    public const UPLOAD_DIRECTORY = 'pages';

    public const IMAGE_PATTERN = '~^(pages|img/illustrations)/[^/]+$~';

    private const URL_PATTERN = '~^(https?://|/|mailto:|tel:|#)~i';

    public static function types(): array
    {
        return [
            'hero' => [
                'label'  => 'Introduction',
                'icon'   => 'saisie',
                'help'   => 'Le grand titre de la page et son texte d\'accroche.',
                'fields' => [
                    'title'        => ['label' => 'Titre', 'type' => 'text', 'required' => true],
                    'text'         => ['label' => 'Texte', 'type' => 'textarea'],
                    'button_label' => ['label' => 'Texte du bouton', 'type' => 'text'],
                    'button_url'   => ['label' => 'Lien du bouton', 'type' => 'url'],
                ],
            ],
            'text' => [
                'label'  => 'Texte et image',
                'icon'   => 'document',
                'help'   => 'Un paragraphe avec un sous-titre et une image à côté.',
                'fields' => [
                    'title' => ['label' => 'Sous-titre', 'type' => 'text'],
                    'text'  => ['label' => 'Texte', 'type' => 'textarea', 'required' => true],
                    'image' => ['label' => 'Image', 'type' => 'image'],
                ],
            ],
            'slots' => [
                'label'  => 'Encadrés illustrés',
                'icon'   => 'galerie',
                'help'   => 'Des encadrés violets avec une illustration, trois par ligne.',
                'fields' => [
                    'title' => ['label' => 'Titre de la section', 'type' => 'text'],
                ],
                'items' => [
                    'label'  => 'Encadré',
                    'min'    => 1,
                    'max'    => 12,
                    'fields' => [
                        'image' => ['label' => 'Illustration', 'type' => 'image'],
                        'icon'  => ['label' => 'Ou une icône', 'type' => 'icon'],
                        'title' => ['label' => 'Titre', 'type' => 'text'],
                        'text'  => ['label' => 'Texte', 'type' => 'textarea', 'required' => true],
                    ],
                ],
            ],
            'features' => [
                'label'  => 'Points clés avec icônes',
                'help'   => 'Une liste courte : une icône, un titre, une phrase.',
                'icon'   => 'taches',
                'fields' => [
                    'title' => ['label' => 'Titre de la section', 'type' => 'text'],
                ],
                'items' => [
                    'label'  => 'Point clé',
                    'min'    => 1,
                    'max'    => 12,
                    'fields' => [
                        'icon'  => ['label' => 'Icône', 'type' => 'icon'],
                        'title' => ['label' => 'Titre', 'type' => 'text', 'required' => true],
                        'text'  => ['label' => 'Texte', 'type' => 'textarea'],
                    ],
                ],
            ],
            'cards' => [
                'label'  => 'Cartes avec lien',
                'icon'   => 'lien',
                'help'   => 'Les cartes de la page d\'accueil, jusqu\'à quatre par ligne.',
                'items'  => [
                    'label'  => 'Carte',
                    'min'    => 1,
                    'max'    => 4,
                    'fields' => [
                        'title' => ['label' => 'Titre', 'type' => 'text', 'required' => true],
                        'text'  => ['label' => 'Texte', 'type' => 'textarea', 'required' => true],
                        'url'   => ['label' => 'Lien « En savoir plus »', 'type' => 'url'],
                        'image' => ['label' => 'Illustration', 'type' => 'image'],
                    ],
                ],
            ],
            'testimonials' => [
                'label'  => 'Témoignages',
                'icon'   => 'discussion',
                'help'   => 'Des citations avec la photo de leur auteur, jusqu\'à quatre.',
                'fields' => [
                    'title' => ['label' => 'Titre de la section', 'type' => 'text'],
                ],
                'items' => [
                    'label'  => 'Témoignage',
                    'min'    => 1,
                    'max'    => 4,
                    'fields' => [
                        'image' => ['label' => 'Photo', 'type' => 'image'],
                        'quote' => ['label' => 'Citation', 'type' => 'textarea', 'required' => true],
                        'name'  => ['label' => 'Nom', 'type' => 'text', 'required' => true],
                        'role'  => ['label' => 'Fonction', 'type' => 'text'],
                    ],
                ],
            ],
            'contact' => [
                'label'  => '« On garde contact ? »',
                'icon'   => 'courriel',
                'help'   => 'Les trois boutons brochure, newsletter et devis.',
                'fields' => [
                    'title' => ['label' => 'Titre', 'type' => 'text', 'required' => true, 'default' => 'On garde contact ? 🤙'],
                ],
            ],
        ];
    }

    public static function make(string $type): array
    {
        $definition = self::types()[$type];

        $block = ['id' => Str::random(8), 'type' => $type] + self::emptyValues($definition['fields'] ?? []);

        if (isset($definition['items'])) {
            // Trois éléments d'emblée pour remplir la première ligne, comme les pages existantes.
            $count = in_array($type, ['slots', 'features']) ? 3 : 1;
            $block['items'] = collect(range(1, $count))->map(fn () => self::makeItem($type))->all();
        }

        return $block;
    }

    public static function makeItem(string $type): array
    {
        return ['id' => Str::random(8)] + self::emptyValues(self::types()[$type]['items']['fields']);
    }

    /** Les blocs d'une nouvelle page : la même structure que les pages de services existantes. */
    public static function template(): array
    {
        return [self::make('hero'), self::make('slots'), self::make('contact')];
    }

    public static function rules(array $blocks): array
    {
        $rules = ['blocks' => ['required', 'array', 'min:1']];

        foreach ($blocks as $i => $block) {
            $definition = self::types()[$block['type'] ?? ''] ?? null;

            $rules["blocks.$i.type"] = ['required', 'in:' . implode(',', array_keys(self::types()))];

            if (! $definition) {
                continue;
            }

            $rules += self::fieldRules("blocks.$i", $definition['fields'] ?? []);

            if (isset($definition['items'])) {
                $rules["blocks.$i.items"] = ['required', 'array', 'min:' . $definition['items']['min'], 'max:' . $definition['items']['max']];

                foreach (array_keys($block['items'] ?? []) as $j) {
                    $rules += self::fieldRules("blocks.$i.items.$j", $definition['items']['fields']);
                }
            }
        }

        return $rules;
    }

    public static function attributes(array $blocks): array
    {
        $attributes = [];

        foreach ($blocks as $i => $block) {
            $definition = self::types()[$block['type'] ?? ''] ?? [];

            foreach ($definition['fields'] ?? [] as $name => $field) {
                $attributes["blocks.$i.$name"] = Str::lower($field['label']);
            }

            foreach (array_keys($block['items'] ?? []) as $j) {
                foreach ($definition['items']['fields'] ?? [] as $name => $field) {
                    $attributes["blocks.$i.items.$j.$name"] = Str::lower($field['label']);
                }
            }
        }

        return $attributes;
    }

    /** Ne conserve que les champs du catalogue, avant enregistrement. */
    public static function clean(array $blocks): array
    {
        return collect($blocks)->map(function ($block) {
            $definition = self::types()[$block['type']];
            $clean      = ['id' => $block['id'], 'type' => $block['type']]
                + self::onlyFields($block, $definition['fields'] ?? []);

            if (isset($definition['items'])) {
                $clean['items'] = collect($block['items'])->map(
                    fn ($item) => ['id' => $item['id']] + self::onlyFields($item, $definition['items']['fields'])
                )->values()->all();
            }

            return $clean;
        })->values()->all();
    }

    public static function imageUrl(?string $path): ?string
    {
        if (blank($path) || ! preg_match(self::IMAGE_PATTERN, $path)) {
            return null;
        }

        return str_starts_with($path, self::UPLOAD_DIRECTORY . '/')
            ? route('pages.media', ['file' => basename($path)])
            : asset($path);
    }

    /** Les données du sélecteur d'images de l'éditeur. */
    public static function imageLibrary(): array
    {
        $disk = Storage::disk('public');

        $uploads = collect($disk->files(self::UPLOAD_DIRECTORY))
            ->sortByDesc(fn ($path) => $disk->lastModified($path))
            ->map(fn ($path) => [
                'value' => $path,
                'label' => 'Envoyée le ' . date('d/m/Y', $disk->lastModified($path)),
                'theme' => 'Images envoyées',
                'url'   => self::imageUrl($path),
            ]);

        $illustrations = collect(File::files(public_path('img/illustrations')))
            ->map(fn ($file) => 'img/illustrations/' . $file->getFilename())
            ->sort()
            ->map(fn ($path) => [
                'value' => $path,
                'label' => Str::of(basename($path))->beforeLast('.')->replace(['_', '&'], ' ')->squish()->toString(),
                'theme' => 'Illustrations',
                'url'   => self::imageUrl($path),
            ]);

        return $uploads->concat($illustrations)->values()->all();
    }

    private static function emptyValues(array $fields): array
    {
        return collect($fields)->map(fn ($field) => $field['default'] ?? '')->all();
    }

    private static function onlyFields(array $values, array $fields): array
    {
        return collect($fields)->map(fn ($field, $name) => $values[$name] ?? '')->all();
    }

    private static function fieldRules(string $prefix, array $fields): array
    {
        $rules = [];

        foreach ($fields as $name => $field) {
            $rules["$prefix.$name"] = array_merge(
                [($field['required'] ?? false) ? 'required' : 'nullable', 'string'],
                match ($field['type']) {
                    'textarea' => ['max:5000'],
                    'url'      => ['max:2048', 'regex:' . self::URL_PATTERN],
                    'image'    => ['regex:' . self::IMAGE_PATTERN],
                    'icon'     => [Rule::in(array_keys(PageIcons::names()))],
                    default    => ['max:255'],
                }
            );
        }

        if (isset($fields['button_url'])) {
            $rules["$prefix.button_url"][] = "required_with:$prefix.button_label";
        }

        return $rules;
    }
}
