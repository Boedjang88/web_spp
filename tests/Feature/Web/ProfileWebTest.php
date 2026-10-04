<?php

namespace Tests\Feature\Web;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileWebTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_authenticated_user_can_view_profile_page(): void
    {
        $response = $this->actingAs($this->user)->get(route('profile.index'));

        $response->assertStatus(200)
            ->assertSee('Informasi Profil')
            ->assertSee('john@test.com');
    }

    public function test_user_can_update_profile_information(): void
    {
        $response = $this->actingAs($this->user)->put(route('profile.update'), [
            'name' => 'John Updated',
            'email' => 'johnupdated@test.com',
        ]);

        $response->assertRedirect(route('profile.index'))
            ->assertSessionHas('success', 'Profil akun Anda berhasil diperbarui.');

        $this->user->refresh();
        $this->assertEquals('John Updated', $this->user->name);
        $this->assertEquals('johnupdated@test.com', $this->user->email);
    }

    public function test_user_can_change_password_with_valid_current_password(): void
    {
        $response = $this->actingAs($this->user)->put(route('profile.password'), [
            'current_password' => 'password123',
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ]);

        $response->assertRedirect(route('profile.index'))
            ->assertSessionHas('success', 'Kata sandi akun Anda berhasil diperbarui.');

        $this->user->refresh();
        $this->assertTrue(Hash::check('newpassword456', $this->user->password));
    }

    public function test_user_cannot_change_password_with_wrong_current_password(): void
    {
        $response = $this->actingAs($this->user)->put(route('profile.password'), [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword456',
            'password_confirmation' => 'newpassword456',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->user->refresh();
        $this->assertFalse(Hash::check('newpassword456', $this->user->password));
    }
}
