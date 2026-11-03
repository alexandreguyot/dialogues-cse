<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasAdvancedFilter;

    public const SLUG_PATTERN = '[a-z0-9]+(?:-[a-z0-9]+)*';

    // Dossiers de public/ : nginx et Apache les serviraient à la place de la page.
    private const RESERVED_SLUGS = ['css', 'js', 'img', 'images', 'pdf', 'storage', 'media', 'vendor'];

    public $table = 'pages';

    protected $fillable = [
        'title',
        'slug',
        'theme',
        'meta_description',
        'in_menu',
        'is_published',
        'blocks',
    ];

    protected $attributes = [
        'theme'        => 'light',
        'in_menu'      => false,
        'is_published' => false,
    ];

    protected $casts = [
        'blocks'       => 'array',
        'in_menu'      => 'boolean',
        'is_published' => 'boolean',
    ];

    public $orderable = [
        'id',
        'title',
        'slug',
        'is_published',
        'updated_at',
    ];

    public $filterable = [
        'id',
        'title',
        'slug',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public static function reservedSlugs(): array
    {
        $routeSegments = collect(app('router')->getRoutes()->getRoutes())
            ->map(fn ($route) => Str::lower(explode('/', $route->uri())[0]))
            ->reject(fn ($segment) => $segment === '' || str_starts_with($segment, '{'));

        return $routeSegments->merge(self::RESERVED_SLUGS)->unique()->values()->all();
    }

    public function duplicate(): self
    {
        $copy = $this->replicate();

        $slug = $this->slug . '-copie';
        for ($i = 2; self::where('slug', $slug)->exists(); $i++) {
            $slug = $this->slug . '-copie-' . $i;
        }

        $copy->fill([
            'title'        => $this->title . ' (copie)',
            'slug'         => $slug,
            'in_menu'      => false,
            'is_published' => false,
        ])->save();

        return $copy;
    }
}
