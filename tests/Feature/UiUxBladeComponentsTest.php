<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class UiUxBladeComponentsTest extends TestCase
{
    /** @test */
    public function it_renders_button_component_as_button_and_anchor(): void
    {
        $buttonHtml = Blade::render('<x-button variant="primary" size="md">Simpan Perubahan</x-button>');
        $this->assertStringContainsString('<button type="button"', $buttonHtml);
        $this->assertStringContainsString('Simpan Perubahan', $buttonHtml);
        $this->assertStringContainsString('bg-brand-600', $buttonHtml);

        $linkHtml = Blade::render('<x-button href="/dashboard" variant="secondary">Kembali</x-button>');
        $this->assertStringContainsString('<a href="/dashboard"', $linkHtml);
        $this->assertStringContainsString('Kembali', $linkHtml);
        $this->assertStringContainsString('bg-slate-100', $linkHtml);
    }

    /** @test */
    public function it_renders_card_component_with_header_and_footer(): void
    {
        $cardHtml = Blade::render('
            <x-card title="Ringkasan Akademik" subtitle="Semester Ganjil 2026/2027">
                Konten Kartu Utama
                <x-slot:footer>Catatan Kaki</x-slot:footer>
            </x-card>
        ');

        $this->assertStringContainsString('Ringkasan Akademik', $cardHtml);
        $this->assertStringContainsString('Semester Ganjil 2026/2027', $cardHtml);
        $this->assertStringContainsString('Konten Kartu Utama', $cardHtml);
        $this->assertStringContainsString('Catatan Kaki', $cardHtml);
        $this->assertStringContainsString('rounded-2xl', $cardHtml);
    }

    /** @test */
    public function it_renders_status_badge_with_semantic_color_mapping(): void
    {
        $successBadge = Blade::render('<x-status-badge status="lunas" />');
        $this->assertStringContainsString('bg-emerald-50', $successBadge);
        $this->assertStringContainsString('Lunas', $successBadge);

        $warningBadge = Blade::render('<x-status-badge status="menunggu" />');
        $this->assertStringContainsString('bg-amber-50', $warningBadge);
        $this->assertStringContainsString('Menunggu', $warningBadge);

        $dangerBadge = Blade::render('<x-status-badge status="belum_lunas" />');
        $this->assertStringContainsString('bg-rose-50', $dangerBadge);
        $this->assertStringContainsString('Belum Lunas', $dangerBadge);
    }

    /** @test */
    public function it_renders_alert_component_with_dismiss_button(): void
    {
        $alertHtml = Blade::render('
            <x-alert type="warning" title="Perhatian" :dismissible="true">
                Batas pengisian KRS berakhir besok.
            </x-alert>
        ');

        $this->assertStringContainsString('role="alert"', $alertHtml);
        $this->assertStringContainsString('Perhatian', $alertHtml);
        $this->assertStringContainsString('Batas pengisian KRS berakhir besok.', $alertHtml);
        $this->assertStringContainsString('aria-label="Tutup notifikasi"', $alertHtml);
    }

    /** @test */
    public function it_renders_modal_component_with_dialog_semantics(): void
    {
        $modalHtml = Blade::render('
            <x-modal name="confirm-delete" title="Konfirmasi Hapus">
                Apakah Anda yakin ingin menghapus data ini?
            </x-modal>
        ');

        $this->assertStringContainsString('role="dialog"', $modalHtml);
        $this->assertStringContainsString('aria-modal="true"', $modalHtml);
        $this->assertStringContainsString('Konfirmasi Hapus', $modalHtml);
        $this->assertStringContainsString('Apakah Anda yakin ingin menghapus data ini?', $modalHtml);
    }
}
