@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
    'icon' => null,
    'iconPosition' => 'left',
    'disabled' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-slate-900 disabled:opacity-60 disabled:cursor-not-allowed select-none active:scale-[0.98]';

    $sizeClasses = [
        'sm' => 'px-3 py-1.5 text-xs rounded-lg gap-1.5 min-h-[34px]',
        'md' => 'px-4 py-2 text-sm rounded-xl gap-2 min-h-[42px]',
        'lg' => 'px-5 py-2.5 text-base rounded-xl gap-2.5 min-h-[48px]',
    ][$size] ?? 'px-4 py-2 text-sm rounded-xl gap-2 min-h-[42px]';

    $variantClasses = [
        'primary' => 'bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white shadow-sm hover:shadow focus:ring-brand-500 border border-transparent',
        'secondary' => 'bg-slate-100 hover:bg-slate-200 active:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 focus:ring-slate-400 border border-slate-200 dark:border-slate-700',
        'outline' => 'bg-transparent hover:bg-slate-50 active:bg-slate-100 dark:hover:bg-slate-800/60 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-600 focus:ring-slate-400',
        'danger' => 'bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white shadow-sm hover:shadow focus:ring-rose-500 border border-transparent',
        'ghost' => 'bg-transparent hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white focus:ring-slate-400 border border-transparent',
    ][$variant] ?? 'bg-brand-600 hover:bg-brand-700 text-white focus:ring-brand-500 border border-transparent';

    $classes = $baseClasses . ' ' . $sizeClasses . ' ' . $variantClasses;
@endphp

@if($href)
    @if($disabled)
        <a role="link" 
           aria-disabled="true" 
           tabindex="-1"
           {{ $attributes->merge(['class' => $classes . ' pointer-events-none opacity-60 cursor-not-allowed']) }}>
            {{ $slot }}
        </a>
    @else
        <a href="{{ $href }}"
           {{ $attributes->merge(['class' => $classes]) }}>
            {{ $slot }}
        </a>
    @endif
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
