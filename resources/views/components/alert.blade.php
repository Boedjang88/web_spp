@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => false,
])

@php
    $normalizedType = match ($type) {
        'success' => 'success',
        'warning' => 'warning',
        'danger', 'error' => 'danger',
        default => 'info',
    };

    $styles = [
        'info' => [
            'box' => 'bg-blue-50/90 text-blue-900 border-blue-200 dark:bg-blue-950/50 dark:text-blue-200 dark:border-blue-900/60',
            'iconColor' => 'text-blue-600 dark:text-blue-400',
            'titleColor' => 'text-blue-950 dark:text-blue-100',
        ],
        'success' => [
            'box' => 'bg-emerald-50/90 text-emerald-900 border-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-200 dark:border-emerald-900/60',
            'iconColor' => 'text-emerald-600 dark:text-emerald-400',
            'titleColor' => 'text-emerald-950 dark:text-emerald-100',
        ],
        'warning' => [
            'box' => 'bg-amber-50/90 text-amber-950 border-amber-200 dark:bg-amber-950/50 dark:text-amber-200 dark:border-amber-900/60',
            'iconColor' => 'text-amber-600 dark:text-amber-400',
            'titleColor' => 'text-amber-950 dark:text-amber-100',
        ],
        'danger' => [
            'box' => 'bg-rose-50/90 text-rose-950 border-rose-200 dark:bg-rose-950/50 dark:text-rose-200 dark:border-rose-900/60',
            'iconColor' => 'text-rose-600 dark:text-rose-400',
            'titleColor' => 'text-rose-950 dark:text-rose-100',
        ],
    ][$normalizedType];
@endphp

<div 
    x-data="{ show: true }" 
    x-show="show" 
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    role="alert" 
    {{ $attributes->merge(['class' => 'p-4 rounded-xl border flex items-start gap-3 shadow-xs ' . $styles['box']]) }}
>
    <div class="shrink-0 mt-0.5 {{ $styles['iconColor'] }}" aria-hidden="true">
        @if($normalizedType === 'success')
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        @elseif($normalizedType === 'warning')
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
        @elseif($normalizedType === 'danger')
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 7.5h.008v.008H12v-.008z" />
            </svg>
        @else
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>
        @endif
    </div>

    <div class="flex-1 text-sm leading-relaxed min-w-0">
        @if($title)
            <h4 class="font-semibold text-sm mb-0.5 {{ $styles['titleColor'] }}">
                {{ $title }}
            </h4>
        @endif
        <div>
            {{ $slot }}
        </div>
    </div>

    @if($dismissible)
        <button 
            type="button" 
            @click="show = false" 
            class="shrink-0 p-1 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400"
            aria-label="Tutup notifikasi"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    @endif
</div>
