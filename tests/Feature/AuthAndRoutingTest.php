<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesScenario;
use Tests\TestCase;

class AuthAndRoutingTest extends TestCase
{
    use RefreshDatabase, CreatesScenario;

    private const PROTECTED_ROUTES = [
        '/',
        '/input-nilai',
        '/rekap-juara',
        '/input-juara-spesial',
        '/master-event',
        '/master-kategori',
        '/master-format-nilai',
        '/master-peserta',
        '/master-juri',
        '/input-denda',
        '/audit-juri',
    ];

    public function test_login_page_is_accessible_for_guest(): void
    {
        $this->get('/login')->assertOk()->assertSee('PANDARA');
    }

    public function test_all_protected_routes_redirect_guests_to_login(): void
    {
        foreach (self::PROTECTED_ROUTES as $uri) {
            $this->get($uri)->assertRedirect('/login');
        }
    }

    public function test_admin_can_login_with_username_and_password(): void
    {
        $this->makeUser('admin', 'admin', 'password123');

        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $this->makeUser('admin', 'admin', 'password123');

        $response = $this->from('/login')->post('/login', [
            'username' => 'admin',
            'password' => 'salah',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = $this->makeUser('admin');

        $this->actingAs($user)->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_admin_can_open_every_page(): void
    {
        $admin = $this->makeUser('admin', 'admin');
        $event = $this->makeEvent();
        $kategori = $this->makeKategori($event, 'PBB', true, true);
        $this->makeItem($kategori);
        $this->makePeserta($event);

        foreach (self::PROTECTED_ROUTES as $uri) {
            $this->actingAs($admin)->get($uri)->assertOk();
        }
    }

    public function test_non_admin_cannot_access_admin_only_routes(): void
    {
        $juriUser = $this->makeUser('juri1', 'juri');

        $response = $this->actingAs($juriUser)->get('/master-event');

        $response->assertRedirect('/');
        $response->assertSessionHas('error');
    }

    public function test_rekap_login_redirects_to_input_nilai(): void
    {
        $this->makeUser('rekap', 'rekap', 'rekap123');

        $response = $this->post('/login', [
            'username' => 'rekap',
            'password' => 'rekap123',
        ]);

        $response->assertRedirect('/input-nilai');
        $this->assertAuthenticated();
    }

    public function test_rekap_can_only_access_input_nilai_and_input_denda(): void
    {
        $rekap = $this->makeUser('rekap', 'rekap');
        $event = $this->makeEvent();
        $kategori = $this->makeKategori($event, 'PBB', true, true);
        $this->makeItem($kategori);
        $this->makePeserta($event);

        $this->actingAs($rekap)->get('/input-nilai')->assertOk();
        $this->actingAs($rekap)->get('/input-denda')
            ->assertOk()
            ->assertDontSee('Kelola Pedoman Event');

        $blockedRoutes = [
            '/',
            '/rekap-juara',
            '/input-juara-spesial',
            '/master-event',
            '/master-kategori',
            '/master-format-nilai',
            '/master-peserta',
            '/master-juri',
            '/audit-juri',
        ];

        foreach ($blockedRoutes as $uri) {
            $response = $this->actingAs($rekap)->get($uri);

            $response->assertRedirect('/input-nilai');
            $response->assertSessionHas('error');
        }
    }
}
