<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class UiUxAccessibilityAndPerformanceTest extends TestCase
{
    /** @test */
    public function it_includes_skip_to_main_content_link_and_accessible_target(): void
    {
        $shellContent = File::get(resource_path('views/layouts/app.blade.php'));

        $this->assertStringContainsString('href="#main-content"', $shellContent);
        $this->assertStringContainsString('sr-only focus:not-sr-only', $shellContent);
        $this->assertStringContainsString('id="main-content"', $shellContent);
        $this->assertStringContainsString('tabindex="-1"', $shellContent);
    }

    /** @test */
    public function it_includes_live_region_attributes_on_toast_container_and_notifications(): void
    {
        $shellContent = File::get(resource_path('views/layouts/app.blade.php'));

        $this->assertStringContainsString('id="toastContainer"', $shellContent);
        $this->assertStringContainsString('role="region"', $shellContent);
        $this->assertStringContainsString('aria-live="polite"', $shellContent);
        $this->assertStringContainsString("toast.setAttribute('role', type === 'error' ? 'alert' : 'status')", $shellContent);
        $this->assertStringContainsString("toast.setAttribute('aria-live', type === 'error' ? 'assertive' : 'polite')", $shellContent);
    }

    /** @test */
    public function it_implements_focus_trapping_and_focus_restoration_in_modal_component(): void
    {
        $view = view('components.modal', [
            'name' => 'test-a11y-modal',
            'title' => 'A11y Test Modal',
            'slot' => '<p>Konten modal ramah aksesibilitas.</p>',
            'footer' => '<button type="button">Tutup</button>',
        ])->render();

        $this->assertStringContainsString('trapFocus', $view);
        $this->assertStringContainsString('triggerEl', $view);
        $this->assertStringContainsString('x-on:keydown.tab="trapFocus($event)"', $view);
        $this->assertStringContainsString('triggerEl.focus()', $view);
    }

    /** @test */
    public function it_configures_alert_modal_with_alertdialog_semantics_and_aria_attributes(): void
    {
        $shellContent = File::get(resource_path('views/layouts/app.blade.php'));

        $this->assertStringContainsString('role="alertdialog"', $shellContent);
        $this->assertStringContainsString('aria-modal="true"', $shellContent);
        $this->assertStringContainsString('aria-labelledby="modalTitle"', $shellContent);
        $this->assertStringContainsString('aria-describedby="modalBody"', $shellContent);
        $this->assertStringContainsString('aria-label="Tutup dialog notifikasi"', $shellContent);
        $this->assertStringContainsString('alertModalTriggerEl', $shellContent);
    }

    /** @test */
    public function it_prevents_disabled_links_from_modifying_url_fragment_or_navigating(): void
    {
        $linkView = view('components.button', [
            'href' => '/academic/krs/export',
            'disabled' => true,
            'slot' => 'Ekspor KRS',
        ])->render();

        $this->assertStringNotContainsString('href=', $linkView);
        $this->assertStringNotContainsString('javascript:', $linkView);
        $this->assertStringContainsString('role="link"', $linkView);
        $this->assertStringContainsString('aria-disabled="true"', $linkView);
        $this->assertStringContainsString('tabindex="-1"', $linkView);
        $this->assertStringContainsString('pointer-events-none', $linkView);
    }

    /** @test */
    public function it_guards_form_submission_with_html5_validation_and_invalid_event_recovery(): void
    {
        $shellContent = File::get(resource_path('views/layouts/app.blade.php'));
        $loginContent = File::get(resource_path('views/auth/login.blade.php'));

        // app.blade.php guards
        $this->assertStringContainsString('form.checkValidity && !form.checkValidity()', $shellContent);
        $this->assertStringContainsString("document.addEventListener('invalid'", $shellContent);
        $this->assertStringContainsString("window.addEventListener('beforeunload'", $shellContent);

        // login.blade.php guards
        $this->assertStringContainsString('form.checkValidity && !form.checkValidity()', $loginContent);
        $this->assertStringContainsString("document.addEventListener('invalid'", $loginContent);
        $this->assertStringContainsString("window.addEventListener('beforeunload'", $loginContent);
    }

    /** @test */
    public function it_allows_pinch_to_zoom_on_public_student_portal_views(): void
    {
        $cekTagihanContent = File::get(resource_path('views/cek-tagihan.blade.php'));
        $verifyContent = File::get(resource_path('views/siakad/diploma/verify.blade.php'));

        $this->assertStringNotContainsString('user-scalable=no', $cekTagihanContent);
        $this->assertStringNotContainsString('maximum-scale=1.0', $cekTagihanContent);
        $this->assertStringContainsString('maximum-scale=5.0', $cekTagihanContent);

        $this->assertStringNotContainsString('user-scalable=no', $verifyContent);
        $this->assertStringNotContainsString('maximum-scale=1.0', $verifyContent);
        $this->assertStringContainsString('maximum-scale=5.0', $verifyContent);
    }

    /** @test */
    public function it_ensures_touch_target_size_minimum_is_44px_for_menu_items(): void
    {
        $menuContent = File::get(resource_path('views/components/navigation/menu-items.blade.php'));

        $this->assertStringContainsString('min-h-[44px]', $menuContent);
    }

    /** @test */
    public function it_preserves_clear_visual_feedback_under_prefers_reduced_motion(): void
    {
        $cssContent = File::get(resource_path('css/app.css'));

        $this->assertStringContainsString('@media (prefers-reduced-motion: reduce)', $cssContent);
        $this->assertStringContainsString('.animate-spin', $cssContent);
        $this->assertStringContainsString('animation: none !important;', $cssContent);
    }

    /** @test */
    public function it_handles_escape_key_in_lifo_order_without_closing_unrelated_overlays(): void
    {
        $shellContent = File::get(resource_path('views/layouts/app.blade.php'));

        $this->assertStringContainsString('closeAlertModal()', $shellContent);
        $this->assertStringContainsString('toggleMobileMenu(true)', $shellContent);
        // Asserts modal check exists before closing mobile sidebar
        $this->assertStringContainsString('role="dialog"', $shellContent);
    }
}
