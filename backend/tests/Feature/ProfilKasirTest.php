<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ProfilKasir;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilKasirTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $kasir1;
    protected User $kasir2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users with roles
        $this->admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $this->kasir1 = User::factory()->create(['role' => 'kasir', 'is_active' => true]);
        $this->kasir2 = User::factory()->create(['role' => 'kasir', 'is_active' => true]);
    }

    public function test_admin_can_fetch_all_profiles()
    {
        ProfilKasir::create([
            'user_id' => $this->kasir1->id,
            'nama' => 'Kasir 1 Profile A',
            'kode_khusus' => '1111',
            'is_active' => false
        ]);

        ProfilKasir::create([
            'user_id' => $this->kasir2->id,
            'nama' => 'Kasir 2 Profile B',
            'kode_khusus' => '2222',
            'is_active' => false
        ]);

        $response = $this->actingAs($this->admin)->getJson('/api/profil-kasir');

        $response->assertStatus(200)
            ->assertJsonFragment(['nama' => 'Kasir 1 Profile A'])
            ->assertJsonFragment(['nama' => 'Kasir 2 Profile B']);
    }

    public function test_kasir_can_only_fetch_own_profiles()
    {
        ProfilKasir::create([
            'user_id' => $this->kasir1->id,
            'nama' => 'Kasir 1 Profile A',
            'kode_khusus' => '1111',
            'is_active' => false
        ]);

        ProfilKasir::create([
            'user_id' => $this->kasir2->id,
            'nama' => 'Kasir 2 Profile B',
            'kode_khusus' => '2222',
            'is_active' => false
        ]);

        $response = $this->actingAs($this->kasir1)->getJson('/api/profil-kasir');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['nama' => 'Kasir 1 Profile A'])
            ->assertJsonMissing(['nama' => 'Kasir 2 Profile B']);
    }

    public function test_admin_can_create_cashier_profile()
    {
        $payload = [
            'user_id' => $this->kasir1->id,
            'nama' => 'New Cashier Profile',
            'kode_khusus' => '123456'
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/profil-kasir', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['nama' => 'New Cashier Profile']);

        $this->assertDatabaseHas('profil_kasirs', [
            'user_id' => $this->kasir1->id,
            'nama' => 'New Cashier Profile',
            'kode_khusus' => '123456',
            'is_active' => false
        ]);
    }

    public function test_admin_cannot_create_profile_for_non_cashier_user()
    {
        $payload = [
            'user_id' => $this->admin->id,
            'nama' => 'Admin Profile',
            'kode_khusus' => '123456'
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/profil-kasir', $payload);

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'Profil hanya dapat dibuat untuk akun dengan role kasir.']);
    }

    public function test_kasir_cannot_create_cashier_profile()
    {
        $payload = [
            'user_id' => $this->kasir1->id,
            'nama' => 'Unpermitted Profile',
            'kode_khusus' => '123456'
        ];

        $response = $this->actingAs($this->kasir1)->postJson('/api/profil-kasir', $payload);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_profile()
    {
        $profile = ProfilKasir::create([
            'user_id' => $this->kasir1->id,
            'nama' => 'Old Name',
            'kode_khusus' => '1111',
            'is_active' => false
        ]);

        $payload = [
            'nama' => 'Updated Name',
            'kode_khusus' => '3333'
        ];

        $response = $this->actingAs($this->admin)->putJson("/api/profil-kasir/{$profile->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment(['nama' => 'Updated Name']);

        $this->assertDatabaseHas('profil_kasirs', [
            'id' => $profile->id,
            'nama' => 'Updated Name',
            'kode_khusus' => '3333'
        ]);
    }

    public function test_kasir_cannot_update_profile()
    {
        $profile = ProfilKasir::create([
            'user_id' => $this->kasir1->id,
            'nama' => 'Old Name',
            'kode_khusus' => '1111',
            'is_active' => false
        ]);

        $payload = [
            'nama' => 'Hacker Name'
        ];

        $response = $this->actingAs($this->kasir1)->putJson("/api/profil-kasir/{$profile->id}", $payload);

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_profile()
    {
        $profile = ProfilKasir::create([
            'user_id' => $this->kasir1->id,
            'nama' => 'To Be Deleted',
            'kode_khusus' => '1111',
            'is_active' => false
        ]);

        $response = $this->actingAs($this->admin)->deleteJson("/api/profil-kasir/{$profile->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('profil_kasirs', ['id' => $profile->id]);
    }

    public function test_kasir_cannot_delete_profile()
    {
        $profile = ProfilKasir::create([
            'user_id' => $this->kasir1->id,
            'nama' => 'Safe Profile',
            'kode_khusus' => '1111',
            'is_active' => false
        ]);

        $response = $this->actingAs($this->kasir1)->deleteJson("/api/profil-kasir/{$profile->id}");

        $response->assertStatus(403);
    }

    public function test_kasir_can_activate_own_profile_with_correct_pin()
    {
        $profile = ProfilKasir::create([
            'user_id' => $this->kasir1->id,
            'nama' => 'My Profile',
            'kode_khusus' => '123456',
            'is_active' => false
        ]);

        $payload = [
            'id' => $profile->id,
            'kode_khusus' => '123456'
        ];

        $response = $this->actingAs($this->kasir1)->postJson('/api/profil-kasir/aktifkan', $payload);

        $response->assertStatus(200)
            ->assertJsonFragment(['is_active' => true]);

        $this->assertTrue($profile->fresh()->is_active);
    }

    public function test_kasir_cannot_activate_own_profile_with_incorrect_pin()
    {
        $profile = ProfilKasir::create([
            'user_id' => $this->kasir1->id,
            'nama' => 'My Profile',
            'kode_khusus' => '123456',
            'is_active' => false
        ]);

        $payload = [
            'id' => $profile->id,
            'kode_khusus' => 'wrongpin'
        ];

        $response = $this->actingAs($this->kasir1)->postJson('/api/profil-kasir/aktifkan', $payload);

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'Kode khusus salah.']);

        $this->assertFalse($profile->fresh()->is_active);
    }

    public function test_kasir_can_deactivate_profile()
    {
        $profile = ProfilKasir::create([
            'user_id' => $this->kasir1->id,
            'nama' => 'Active Profile',
            'kode_khusus' => '123456',
            'is_active' => true
        ]);

        $response = $this->actingAs($this->kasir1)->postJson('/api/profil-kasir/nonaktifkan');

        $response->assertStatus(200);
        $this->assertFalse($profile->fresh()->is_active);
    }
}
