<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\Contact;
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

    public function sendEmail(Request $request) {
        $validate = Validator::make($request->all(), [
            'g-recaptcha-response' => 'required|captcha'
        ]);

        if($validate->fails()){
            return redirect()->back()->withErrors($validate);
        }

        Mail::to('bonjourdialogues@gmail.fr')->queue(new Contact($request->except('_token')));

        toastr()->success('Mail envoyé, je vous recontacterai !', 'Envoyé !');

        return view('site.home');
    }
}
