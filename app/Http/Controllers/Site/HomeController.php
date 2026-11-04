<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\Contact;
use App\Models\Page;
use Illuminate\Support\Facades\Mail;
use Validator;

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

    public function sitemap() {
        $urls = collect(['home', 'identite', 'communiquer', 'reunions', 'site', 'devis'])
            ->map(fn ($name) => route($name))
            ->merge(Page::published()->pluck('slug')->map(fn ($slug) => route('pages.show', $slug)));

        // Hors du template : avec short_open_tag, Blade prendrait cette ligne pour du PHP.
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . view('site.sitemap', ['urls' => $urls])->render();

        return response($xml)->header('Content-Type', 'application/xml');
    }

    public function sendEmail(Request $request) {
        $validate = Validator::make($request->all(), [
            'lastname'             => 'required|string|max:255',
            'email'                => 'required|email|max:255',
            'tel'                  => 'nullable|string|max:50',
            'societe'              => 'nullable|string|max:255',
            'date'                 => 'nullable|date',
            'message'              => 'required|string|max:5000',
            'g-recaptcha-response' => 'required|captcha'
        ]);

        if($validate->fails()){
            return redirect()->back()->withErrors($validate)->withInput();
        }

        foreach (['bonjourdialogues@gmail.com', 'a.pro.guyot@gmail.com'] as $recipient) {
            Mail::to($recipient)->queue(new Contact($validate->safe()->except('g-recaptcha-response')));
        }

        return redirect()->route('home')->with('status', 'Mail envoyé, je vous recontacterai !');
    }
}
