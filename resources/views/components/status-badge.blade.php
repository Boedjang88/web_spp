@props([
    'status' => 'neutral',
    'size' => 'md',
    'dot' => true,
])

@php
    $normalizedStatus = strtolower(trim((string)$status));

    // Map common SIAKAD/SPP business terms to semantic variants
    $variant = match ($normalizedStatus) {
        'success', 'lunas', 'paid', 'approved', 'disetujui', 'aktif', 'selesai', 'verified', 'diterima' => 'success',
        'warning', 'pending', 'menunggu', 'proses', 'cicilan', 'verifikasi' => 'warning',
        'danger', 'error', 'failed', 'gagal', 'belum_lunas', 'belum lunas', 'ditolak', 'rejected', 'terlambat', 'overdue' => 'danger',
        'info', 'draft', 'diajukan', 'baru' => 'info',
        default => 'neutral',
    };

    $variantStyles = [
        'success' => [
            'badge' => 'bg-emerald-50 text-emerald-800 border-emerald-200/80 dark:bg-emerald-950/60 dark:text-emerald-300 dark:border-emerald-800/60',
            'dot' => 'bg-emerald-500',
        ],
        'warning' => [
            'badge' => 'bg-amber-50 text-amber-900 border-amber-200/80 dark:bg-amber-950/60 dark:text-amber-300 dark:border-amber-800/60',
            'dot' => 'bg-amber-500',
        ],
        'danger' => [
            'badge' => 'bg-rose-50 text-rose-900 border-rose-200/80 dark:bg-rose-950/60 dark:text-rose-300 dark:border-rose-800/60',
            'dot' => 'bg-rose-500',
        ],
        'info' => [
            'badge' => 'bg-blue-50 text-blue-900 border-blue-200/80 dark:bg-blue-950/60 dark:text-blue-300 dark:border-blue-800/60',
            'dot' => 'bg-blue-500',
        ],
        'neutral' => [
            'badge' => 'bg-slate-100 text-slate-800 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
            'dot' => 'bg-slate-400',
        ],
    ][$variant];

    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-[11px] gap-1 rounded-md font-medium',
        'md' => 'px-2.5 py-1 text-xs gap-1.5 rounded-lg font-semibold',
    ][$size] ?? 'px-2.5 py-1 text-xs gap-1.5 rounded-lg font-semibold';
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center border select-none transition-colors ' . $sizeClasses . ' ' . $variantStyles['badge']]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $variantStyles['dot'] }} shrink-0" aria-hidden="true"></span>
    @endif
    <span>{{ $slot->isNotEmpty() ? $slot : ucwords(str_replace('_', ' ', $status)) }}</span>
</span>
