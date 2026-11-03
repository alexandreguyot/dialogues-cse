<?php

namespace App\Http\Controllers\Admin;

use App\Models\Page;

class HomeController
{
    public function index()
    {
        return view('admin.home', [
            'publishedCount' => Page::published()->count(),
            'draftCount'     => Page::where('is_published', false)->count(),
            'recentPages'    => Page::latest('updated_at')->limit(5)->get(['id', 'title', 'slug', 'is_published', 'updated_at']),
        ]);
    }
}
