<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class UiUxActionFlowAndInteractionsTest extends TestCase
{
    /** @test */
    public function it_renders_modal_with_scroll_lock_and_keyboard_focus_management(): void
    {
        $view = view('components.modal', [
            'name' => 'test-modal',
            'title' => 'Konfirmasi Transaksi',
            'slot' => '<p>Apakah Anda yakin ingin melanjutkan transaksi ini?</p>',
            'footer' => '<button type="button">Tutup</button>',
        ])->render();

        // 1. Accessibility attributes
        $this->assertStringContainsString('role="dialog"', $view);
        $this->assertStringContainsString('aria-modal="true"', $view);
        $this->assertStringContainsString('aria-labelledby="test-modal-title"', $view);

        // 2. Keyboard Escape listener
        $this->assertStringContainsString('x-on:keydown.escape.window="show = false"', $view);

        // 3. Scroll lock watch and auto-focus logic
        $this->assertStringContainsString('overflow-hidden', $view);
        $this->assertStringContainsString('$watch(\'show\'', $view);
    }

    /** @test */
    public function it_renders_button_with_accessible_disabled_states_for_both_button_and_link(): void
    {
        // 1. Native button disabled
        $buttonView = view('components.button', [
            'disabled' => true,
            'slot' => 'Simpan Data',
        ])->render();

        $this->assertStringContainsString('disabled', $buttonView);
        $this->assertStringContainsString('disabled:opacity-60', $buttonView);
        $this->assertStringContainsString('disabled:cursor-not-allowed', $buttonView);

        // 2. Anchor tag link disabled (accessible aria-disabled and pointer-events-none)
        $linkView = view('components.button', [
            'href' => '/download',
            'disabled' => true,
            'slot' => 'Unduh Berkas',
        ])->render();

        $this->assertStringContainsString('aria-disabled="true"', $linkView);
        $this->assertStringContainsString('tabindex="-1"', $linkView);
        $this->assertStringContainsString('pointer-events-none', $linkView);
    }

    /** @test */
    public function it_includes_double_submission_protection_in_application_shell(): void
    {
        $response = $this->get('/login');
        $response->assertOk();

        // Check app.blade.php / login.blade.php submit prevention
        $response->assertSee('defaultPrevented');
        $response->assertSee('aria-busy');
        $response->assertSee('Memproses Masuk...');
    }

    /** @test */
    public function it_includes_reduced_motion_accessibility_rules_in_css(): void
    {
        $cssPath = resource_path('css/app.css');
        $this->assertTrue(File::exists($cssPath));
        $content = File::get($cssPath);

        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $content);
        $this->assertStringContainsString('animation-duration: 0.01ms !important;', $content);
        $this->assertStringContainsString('transition-duration: 0.01ms !important;', $content);
    }

    /** @test */
    public function it_manages_modal_and_mobile_menu_escape_listeners_in_master_shell(): void
    {
        $shellContent = File::get(resource_path('views/layouts/app.blade.php'));

        $this->assertStringContainsString('toggleMobileMenu(true)', $shellContent);
        $this->assertStringContainsString('closeAlertModal()', $shellContent);
        $this->assertStringContainsString('overflow-hidden', $shellContent);
        $this->assertStringContainsString('defaultPrevented', $shellContent);
    }
}
