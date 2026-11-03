<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\PageBlocks;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::published()->where('slug', $slug)->firstOrFail();

        return view('site.page', compact('page'));
    }

    // Servi par Laravel : le conteneur nginx n'a pas accès au volume storage.
    public function media(string $file)
    {
        $path = PageBlocks::UPLOAD_DIRECTORY . '/' . $file;

        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path, null, [
            'Cache-Control'          => 'public, max-age=2592000',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
