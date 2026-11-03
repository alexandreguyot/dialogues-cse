<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\TwoFactor;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    private function enroll(User $user): string
    {
        $user->two_factor_secret       = $secret = TwoFactor::generateSecret();
        $user->two_factor_confirmed_at = now();
        $user->save();

        return $secret;
    }

    private function code(string $secret): string
    {
        return (new Google2FA())->getCurrentOtp($secret);
    }

    public function test_admin_without_authenticator_must_set_it_up_and_has_no_rights(): void
    {
        $this->post('/login', ['email' => 'admin@admin.com', 'password' => 'password'])->assertRedirect('/admin');

        $this->get('/admin')->assertRedirect(route('two-factor.setup'));
        $this->get('/admin/users')->assertRedirect(route('two-factor.setup'));
        $this->get(route('two-factor.setup'))->assertOk()->assertSee('QR code');
    }

    public function test_setup_confirms_secret_and_shows_recovery_codes_once(): void
    {
        $user = User::findOrFail(1);
        $this->actingAs($user)->get(route('two-factor.setup'))->assertOk();
        $secret = session('two_factor_pending_secret');

        $this->post(route('two-factor.confirm'), ['code' => '000000'])->assertSessionHasErrors('code');
        $this->assertFalse($user->fresh()->hasTwoFactor());

        $this->post(route('two-factor.confirm'), ['code' => $this->code($secret)])->assertRedirect(route('two-factor.recovery'));

        $this->assertTrue($user->fresh()->hasTwoFactor());
        $this->assertNotSame($secret, $user->fresh()->getRawOriginal('two_factor_secret'));
        $this->assertCount(8, $user->fresh()->two_factor_recovery_codes);
        $this->get(route('two-factor.recovery'))->assertOk();
        $this->get(route('two-factor.recovery'))->assertRedirect('/admin');
        $this->get('/admin')->assertOk();
    }

    public function test_login_with_authenticator_requires_code_and_rejects_reuse(): void
    {
        $user   = User::findOrFail(1);
        $secret = $this->enroll($user);

        $this->post('/login', ['email' => 'admin@admin.com', 'password' => 'password']);
        $this->get('/admin')->assertRedirect(route('two-factor.challenge'));
        $this->get('/profile')->assertRedirect(route('two-factor.challenge'));

        $this->post(route('two-factor.verify'), ['code' => '000000'])->assertSessionHasErrors('code');
        $this->get('/admin')->assertRedirect(route('two-factor.challenge'));

        $code = $this->code($secret);
        $this->post(route('two-factor.verify'), ['code' => $code])->assertRedirect('/admin');
        $this->get('/admin')->assertOk();

        $this->assertFalse(TwoFactor::verify($secret, $code, $user->id));
    }

    public function test_recovery_code_works_once(): void
    {
        $user = User::findOrFail(1);
        $this->enroll($user);
        $user->two_factor_recovery_codes = [Hash::make('abcde-fghij')];
        $user->save();

        $this->actingAs($user)->post(route('two-factor.verify'), ['code' => 'abcde-fghij'])->assertRedirect('/admin');
        $this->assertSame([], $user->fresh()->two_factor_recovery_codes);
    }

    public function test_password_alone_grants_no_gate_on_livewire_requests(): void
    {
        $user = User::findOrFail(1);
        $this->enroll($user);

        $this->actingAs($user)->get('/');
        $this->assertTrue(\Illuminate\Support\Facades\Gate::denies('user_delete'));
    }

    public function test_non_admin_is_not_forced_and_can_disable_but_admin_cannot(): void
    {
        $client = User::factory()->create(['email_verified_at' => null]);
        $client->roles()->attach(2);

        $this->actingAs($client)->get('/admin/pages')->assertOk();

        $secret = $this->enroll($client);
        $this->withSession(['two_factor_passed_for' => $client->id])
            ->delete(route('two-factor.disable'), ['code' => $this->code($secret)])
            ->assertRedirect(route('profile.show'));
        $this->assertFalse($client->fresh()->hasTwoFactor());

        $admin  = User::findOrFail(1);
        $secret = $this->enroll($admin);
        $this->actingAs($admin)->withSession(['two_factor_passed_for' => 1])
            ->delete(route('two-factor.disable'), ['code' => $this->code($secret)])
            ->assertForbidden();
    }

    public function test_create_admin_replaces_default_account(): void
    {
        Artisan::call('admin:create', ['email' => 'Moi@Example.com', 'name' => 'Moi', '--replace' => 'admin@admin.com']);

        $user = User::findOrFail(1);
        $this->assertSame('moi@example.com', $user->email);
        $this->assertFalse(Hash::check('password', $user->password));
        $this->assertTrue($user->is_admin);
        $this->assertSame(1, User::count());
    }
}
