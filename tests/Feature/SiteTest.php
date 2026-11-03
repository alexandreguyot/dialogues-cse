<?php

namespace Tests\Feature;

use App\Mail\Contact;
use Anhskohbo\NoCaptcha\Facades\NoCaptcha;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render(): void
    {
        foreach (['/', '/une-identite-visuelle-qui-vous-ressemble', '/communiquer-au-quotidien', '/vous-aider-lors-des-reunions-CSE', '/un-site-web-qui-parle-pour-vous', '/demander-un-devis'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_quote_form_has_csrf_token(): void
    {
        $this->get('/demander-un-devis')->assertSee('name="_token"', false);
    }

    public function test_quote_form_sends_mail_that_renders(): void
    {
        Mail::fake();
        NoCaptcha::shouldReceive('verifyResponse')->andReturn(true);

        $this->post('/envoyer-demander-devis', [
            'lastname'             => 'Jeanne Martin',
            'email'                => 'jeanne@example.com',
            'tel'                  => '0102030405',
            'societe'              => 'ACME',
            'message'              => 'Bonjour',
            'g-recaptcha-response' => 'ok',
        ])->assertRedirect('/')->assertSessionHas('status');

        Mail::assertQueued(Contact::class, function (Contact $mail) {
            $html = $mail->render();

            return str_contains($html, 'Jeanne Martin') && str_contains($html, '0102030405');
        });
    }

    public function test_quote_form_validates_input(): void
    {
        Mail::fake();
        NoCaptcha::shouldReceive('verifyResponse')->andReturn(true);

        $this->post('/envoyer-demander-devis', ['g-recaptcha-response' => 'ok', 'email' => 'pas-un-mail'])
            ->assertSessionHasErrors(['lastname', 'email', 'message']);

        Mail::assertNothingQueued();
    }
}
