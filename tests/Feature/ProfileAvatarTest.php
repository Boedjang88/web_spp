<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileAvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_profile_page()
    {
        $user = User::factory()->create([
            'name' => 'MUHAMMAD LUAYYI ATHOILLAH',
            'email' => 'luayyi@example.com',
            'role' => 'mahasiswa',
        ]);

        $response = $this->actingAs($user)->get(route('profile.index'));
        $response->assertStatus(200);
        $response->assertSee('MUHAMMAD LUAYYI ATHOILLAH');
    }

    public function test_user_can_upload_avatar_photo()
    {
        $user = User::factory()->create([
            'name' => 'MUHAMMAD LUAYYI ATHOILLAH',
            'email' => 'luayyi@example.com',
            'role' => 'mahasiswa',
        ]);

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'MUHAMMAD LUAYYI ATHOILLAH UPDATED',
            'email' => 'luayyi@example.com',
            'avatar' => $file,
        ]);

        $response->assertRedirect(route('profile.index'));
        $this->assertNotNull($user->fresh()->avatar_path);
        $this->assertFileExists(public_path($user->fresh()->avatar_path));

        // Cleanup created file
        if (file_exists(public_path($user->fresh()->avatar_path))) {
            unlink(public_path($user->fresh()->avatar_path));
        }
    }
}
