<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home() {
        return view('site.home');
    }

    public function identite() {
        return view('site.identite');
    }

    public function communiquer() {
        return view('site.communiquer');
    }

    public function site() {
        return view('site.site');
    }

    public function reunions() {
        return view('site.reunions');
    }

    public function devis() {
        return view('site.devis');
    }
}
