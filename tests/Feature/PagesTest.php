<?php

namespace Tests\Feature;

use App\Livewire\Page\Create;
use App\Livewire\Page\Edit;
use App\Livewire\Page\Index;
use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use App\Support\PageBlocks;
use App\Support\PageIcons;
use App\Support\TwoFactor;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    private function page(array $attributes = []): Page
    {
        $hero          = PageBlocks::make('hero');
        $hero['title'] = 'Titre de test';

        return Page::create($attributes + [
            'title'        => 'Ma page',
            'slug'         => 'ma-page',
            'theme'        => 'light',
            'is_published' => true,
            'blocks'       => [$hero],
        ]);
    }

    private function enrolledAdmin(): User
    {
        $user                          = User::findOrFail(1);
        $user->two_factor_secret       = TwoFactor::generateSecret();
        $user->two_factor_confirmed_at = now();
        $user->save();

        return $user;
    }

    private function admin(): User
    {
        $user = User::findOrFail(1);
        $this->actingAs($user);

        // AuthGates (middleware) ne s'exécute pas sous Livewire::test.
        foreach (Role::findOrFail(1)->permissions as $permission) {
            Gate::define($permission->title, fn (User $u) => $u->roles->contains('id', 1));
        }

        return $user;
    }

    public function test_published_page_is_public_and_draft_is_not(): void
    {
        $this->page();
        $this->page(['slug' => 'brouillon', 'is_published' => false]);

        $this->get('/ma-page')->assertOk()->assertSee('Titre de test')->assertSee('<title>Ma page | Dialogues CSE</title>', false);
        $this->get('/brouillon')->assertNotFound();
    }

    public function test_every_block_type_renders_in_both_themes(): void
    {
        $blocks = collect(array_keys(PageBlocks::types()))->map(function ($type) {
            $block = PageBlocks::make($type);

            foreach (['title', 'text'] as $field) {
                if (array_key_exists($field, $block)) {
                    $block[$field] = "$type $field";
                }
            }

            foreach ($block['items'] ?? [] as $k => $item) {
                foreach (['title', 'text', 'quote', 'name'] as $field) {
                    if (array_key_exists($field, $item)) {
                        $block['items'][$k][$field] = "$type item $field";
                    }
                }
            }

            return $block;
        })->all();

        foreach (['light', 'dark'] as $theme) {
            $slug = 'page-' . $theme;
            $this->page(['slug' => $slug, 'theme' => $theme, 'blocks' => $blocks]);

            $this->get("/$slug")->assertOk()
                ->assertSee("<body class=\"$theme\">", false)
                ->assertSee('testimonials item quote')
                ->assertSee('cards item title')
                ->assertSee('slots item text');
        }
    }

    public function test_content_is_escaped(): void
    {
        $hero          = PageBlocks::make('hero');
        $hero['title'] = '<script>alert(1)</script>';
        $hero['text']  = "<b>x</b>\nligne 2";
        $this->page(['blocks' => [$hero]]);

        $this->get('/ma-page')->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('<b>x</b>', false)
            ->assertSee('ligne 2');
    }

    public function test_menu_lists_only_published_pages_flagged_for_menu(): void
    {
        $this->page(['title' => 'Dans le menu', 'in_menu' => true]);
        $this->page(['title' => 'Brouillon menu', 'slug' => 'b', 'in_menu' => true, 'is_published' => false]);

        $this->get('/')->assertOk()->assertSee('Dans le menu')->assertDontSee('Brouillon menu');
    }

    public function test_existing_routes_take_priority_over_pages(): void
    {
        $this->get('/demander-un-devis')->assertOk()->assertSee('Besoin d');
    }

    public function test_admin_requires_login_and_permission(): void
    {
        $this->get('/admin/pages')->assertRedirect('/login');

        $user = User::factory()->create(['email_verified_at' => null]);
        $this->actingAs($user)->get('/admin/pages')->assertForbidden();

        $this->actingAs($this->enrolledAdmin())->withSession(['two_factor_passed_for' => 1])->get('/admin/pages')->assertOk();
    }

    public function test_user_role_can_manage_pages_but_not_users(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $user->roles()->attach(2);

        $this->actingAs($user)->get('/admin/pages/create')->assertOk();
        $this->actingAs($user)->get('/admin/users')->assertForbidden();
    }

    public function test_page_can_be_created_from_the_template(): void
    {
        $this->admin();

        $component = Livewire::test(Create::class)
            ->set('title', 'Élections CSE')
            ->assertSet('slug', 'elections-cse')
            ->set('blocks.0.title', 'Préparer les élections')
            ->set('blocks.1.items.0.text', 'Un')
            ->set('blocks.1.items.1.text', 'Deux')
            ->set('blocks.1.items.2.text', 'Trois')
            ->set('isPublished', true)
            ->call('submit')
            ->assertHasNoErrors();

        $page = Page::where('slug', 'elections-cse')->firstOrFail();
        $component->assertRedirect(route('admin.pages.edit', $page));

        $this->get('/elections-cse')->assertOk()->assertSee('Préparer les élections')->assertSee('Deux');
    }

    public function test_slug_cannot_shadow_site_routes_or_public_folders(): void
    {
        $this->admin();

        foreach (['admin', 'login', 'demander-un-devis', 'css', 'img', 'media'] as $slug) {
            Livewire::test(Create::class)
                ->set('title', 'x')
                ->set('slug', $slug)
                ->call('submit')
                ->assertHasErrors(['slug' => 'not_in']);
        }
    }

    public function test_unsafe_links_are_rejected(): void
    {
        $this->admin();

        Livewire::test(Create::class)
            ->set('title', 'x')
            ->set('blocks.0.title', 'x')
            ->set('blocks.0.button_label', 'Clic')
            ->set('blocks.0.button_url', 'javascript:alert(1)')
            ->call('submit')
            ->assertHasErrors(['blocks.0.button_url' => 'regex']);
    }

    public function test_blocks_can_be_added_moved_and_removed(): void
    {
        $this->admin();

        Livewire::test(Create::class)
            ->call('addBlock', 'text')
            ->assertSet('blocks.2.type', 'text')
            ->assertSet('blocks.3.type', 'contact')
            ->call('moveBlock', 2, -1)
            ->assertSet('blocks.1.type', 'text')
            ->assertSet('blocks.2.type', 'slots')
            ->call('removeBlock', 1)
            ->assertCount('blocks', 3)
            ->call('addItem', 1)
            ->assertCount('blocks.1.items', 4)
            ->call('removeItem', 1, 0)
            ->assertCount('blocks.1.items', 3);
    }

    public function test_image_upload_is_stored_and_served(): void
    {
        Storage::fake('public');
        $this->admin();

        $component = Livewire::test(Create::class)
            ->set('uploads.blocks-1-items-0-image', UploadedFile::fake()->image('photo.png'))
            ->assertHasNoErrors()
            ->assertSet('uploads', []);

        $path = $component->get('blocks')[1]['items'][0]['image'];

        $this->assertStringStartsWith('pages/', $path);
        Storage::disk('public')->assertExists($path);
        $this->get(PageBlocks::imageUrl($path))->assertOk();
    }

    public function test_non_image_upload_is_rejected(): void
    {
        Storage::fake('public');
        $this->admin();

        Livewire::test(Create::class)
            ->set('uploads.blocks-1-items-0-image', UploadedFile::fake()->create('virus.svg', 10, 'image/svg+xml'))
            ->assertHasErrors('blocks.1.items.0.image')
            ->assertSet('blocks.1.items.0.image', '');

        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_page_can_be_edited_duplicated_and_deleted(): void
    {
        $this->admin();
        $page = $this->page();

        Livewire::test(Edit::class, ['page' => $page])
            ->assertSet('blocks.0.title', 'Titre de test')
            ->set('title', 'Nouveau titre')
            ->call('submit')
            ->assertHasNoErrors();
        $this->assertSame('Nouveau titre', $page->fresh()->title);
        $this->assertSame('ma-page', $page->fresh()->slug);

        Livewire::test(Index::class)->call('duplicate', $page->id);
        $copy = Page::where('slug', 'ma-page-copie')->firstOrFail();
        $this->assertFalse($copy->is_published);

        Livewire::test(Index::class)->call('delete', $copy->id);
        $this->assertNull($copy->fresh());
    }

    public function test_icon_library_excludes_basket_and_contains_only_safe_svg(): void
    {
        $names = PageIcons::names();

        $this->assertGreaterThan(100, count($names));
        $this->assertArrayHasKey('calendrier', $names);
        $this->assertArrayNotHasKey('ballon', $names);

        foreach (array_keys($names) as $name) {
            $this->assertDoesNotMatchRegularExpression('/<script|\son\w+=|href=/i', PageIcons::svg($name), $name);
        }

        $this->assertNull(PageIcons::svg('../../.env'));
    }

    public function test_icon_block_renders_and_rejects_unknown_icon(): void
    {
        $this->admin();

        $component = Livewire::test(Create::class)
            ->set('title', 'Avec icônes')
            ->set('slug', 'avec-icones')
            ->set('blocks.0.title', 'Titre')
            ->set('blocks.1.items.0.text', 'a')
            ->set('blocks.1.items.1.text', 'b')
            ->set('blocks.1.items.2.text', 'c')
            ->call('addBlock', 'features')
            ->set('blocks.2.items.0.title', 'Vote')
            ->set('blocks.2.items.0.icon', '../../.env')
            ->set('blocks.2.items.1.title', 'x')
            ->set('blocks.2.items.2.title', 'y')
            ->set('isPublished', true)
            ->call('submit')
            ->assertHasErrors(['blocks.2.items.0.icon' => 'in']);

        $component->set('blocks.2.items.0.icon', 'vote')->call('submit')->assertHasNoErrors();

        $this->get('/avec-icones')->assertOk()->assertSee('Vote')->assertSee('<svg class="icone"', false);
    }

    public function test_preview_works_on_incomplete_content(): void
    {
        $this->admin();

        Livewire::test(Create::class)
            ->call('preview')
            ->assertHasNoErrors()
            ->assertDispatched('page-preview');
    }

    public function test_dashboard_lists_recent_pages(): void
    {
        $this->page(['title' => 'Page récente']);

        $this->actingAs($this->enrolledAdmin())->withSession(['two_factor_passed_for' => 1])->get('/admin')->assertOk()->assertSee('Page récente')->assertSee('3 pages en ligne');
    }

    public function test_draft_preview_is_reserved_to_admins(): void
    {
        $page = $this->page(['is_published' => false]);

        $this->get(route('admin.pages.show', $page))->assertRedirect('/login');
        $this->actingAs($this->enrolledAdmin())->withSession(['two_factor_passed_for' => 1])->get(route('admin.pages.show', $page))->assertOk()->assertSee('Aperçu');
    }

    public function test_upgrade_migration_grants_permissions_on_existing_install(): void
    {
        $this->assertTrue(Role::findOrFail(1)->permissions->contains('title', 'page_edit'));
        $this->assertTrue(Role::findOrFail(2)->permissions->contains('title', 'page_edit'));
    }
}
