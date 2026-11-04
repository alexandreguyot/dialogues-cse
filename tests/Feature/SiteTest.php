<?php

namespace Tests\Feature;

use App\Mail\Contact;
use App\Models\Page;
use Anhskohbo\NoCaptcha\Facades\NoCaptcha;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    private const PUBLIC_URLS = ['/', '/une-identite-visuelle-qui-vous-ressemble', '/communiquer-au-quotidien', '/vous-aider-lors-des-reunions-CSE', '/un-site-web-qui-parle-pour-vous', '/demander-un-devis'];

    public function test_public_pages_render(): void
    {
        foreach (self::PUBLIC_URLS as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_public_pages_have_their_own_seo_tags(): void
    {
        $titles = [];

        foreach (self::PUBLIC_URLS as $url) {
            $html = $this->get($url)->assertOk()->getContent();

            $this->assertMatchesRegularExpression('#<meta name="description" content="[^"]{50,160}"#', $html);
            $this->assertStringContainsString('<link rel="canonical" href="' . url($url === '/' ? '' : $url) . '"', $html);
            $this->assertMatchesRegularExpression('#<meta property="og:image" content="https?://#', $html);
            $this->assertSame(1, preg_match_all('#<h1[ >]#', $html));

            preg_match('#<title>(.*)</title>#', $html, $title);
            $titles[] = $title[1];
        }

        $this->assertSame($titles, array_unique($titles));
        $this->get('/')->assertSee('"@context":"https://schema.org","@type":"Organization"', false);
    }

    public function test_public_page_images_exist_and_have_alt(): void
    {
        foreach (self::PUBLIC_URLS as $url) {
            preg_match_all('#<img [^>]*>#', $this->get($url)->getContent(), $images);

            foreach ($images[0] as $image) {
                $this->assertStringContainsString(' alt="', $image, $url);

                preg_match('#src="([^"]+)"#', $image, $src);
                $this->assertFileExists(public_path(ltrim(parse_url($src[1], PHP_URL_PATH), '/')), $url);
            }
        }
    }

    public function test_sitemap_lists_public_pages_but_not_drafts(): void
    {
        Page::create(['title' => 'Publiée', 'slug' => 'page-publiee', 'is_published' => true, 'blocks' => []]);
        Page::create(['title' => 'Brouillon', 'slug' => 'page-brouillon', 'is_published' => false, 'blocks' => []]);

        $response = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml');

        foreach (self::PUBLIC_URLS as $url) {
            $response->assertSee('<loc>' . url($url === '/' ? '' : $url) . '</loc>', false);
        }

        $response->assertSee('page-publiee')->assertDontSee('page-brouillon');
        $this->assertNotFalse(simplexml_load_string($response->getContent()));
    }

    public function test_legal_pages_are_linked_and_terms_stay_draft_until_completed(): void
    {
        $home = $this->get('/')->assertOk();

        $home->assertSee(route('pages.show', 'mentions-legales'), false);
        $home->assertSee(route('pages.show', 'politique-de-confidentialite'), false);
        $home->assertDontSee(route('pages.show', 'conditions-generales'), false);

        $this->get('/mentions-legales')->assertOk()->assertSee('106 669 393')->assertSee('OVH SAS');
        $this->get('/politique-de-confidentialite')->assertOk()->assertSee('reCAPTCHA');
        $this->get('/conditions-generales')->assertNotFound();

        foreach (['mentions-legales', 'politique-de-confidentialite'] as $slug) {
            $this->assertStringNotContainsString('À COMPLÉTER', $this->get('/' . $slug)->getContent());
        }

        Page::where('slug', 'conditions-generales')->update(['is_published' => true]);
        $this->get('/')->assertSee(route('pages.show', 'conditions-generales'), false);
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
        Mail::assertQueued(Contact::class, 2);
        Mail::assertQueued(Contact::class, fn (Contact $mail) => $mail->hasTo('bonjourdialogues@gmail.com'));
        Mail::assertQueued(Contact::class, fn (Contact $mail) => $mail->hasTo('a.pro.guyot@gmail.com'));
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
