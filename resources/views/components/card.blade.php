@props([
    'title' => null,
    'subtitle' => null,
    'padding' => true,
    'header' => null,
    'action' => null,
    'footer' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl shadow-sm transition-colors overflow-hidden']) }}>
    @if($title || $header || $action)
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-4">
            @if($header)
                {{ $header }}
            @else
                <div class="min-w-0 flex-1">
                    @if($title)
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white truncate">
                            {{ $title }}
                        </h3>
                    @endif
                    @if($subtitle)
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                            {{ $subtitle }}
                        </p>
                    @endif
                </div>
            @endif

            @if($action)
                <div class="shrink-0 flex items-center gap-2">
                    {{ $action }}
                </div>
            @endif
        </div>
    @endif

    <div @class(['p-5' => $padding])>
        {{ $slot }}
    </div>

    @if($footer)
        <div class="px-5 py-3 bg-slate-50/75 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800/80 text-xs text-slate-500 dark:text-slate-400">
            {{ $footer }}
        </div>
    @endif
</div>
