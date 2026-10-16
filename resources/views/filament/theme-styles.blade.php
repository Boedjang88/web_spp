{{-- Plus Jakarta Sans Font Family --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

<style>
    /* ==========================================================================
       Global Enterprise UI - Typography Hierarchy, Elevation & Standardized Radii
       ========================================================================== */
    body, .fi-body, .fi-page {
        font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* Typographic Hierarchy */
    h1, .fi-header-heading {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-weight: 700 !important;
        letter-spacing: -0.025em !important;
        line-height: 1.25 !important;
    }

    h2, .fi-section-header-heading {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-weight: 600 !important;
        letter-spacing: -0.02em !important;
        line-height: 1.35 !important;
    }

    h3, .fi-card-header-heading, .fi-ta-header-heading {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        font-weight: 600 !important;
        letter-spacing: -0.01em !important;
    }

    /* Standardized Border Radiuses (Cards: rounded-xl, Buttons/Inputs: rounded-lg) */
    .fi-section,
    .fi-wi-stats-overview-stat,
    .fi-ta-ctn,
    .fi-modal-window,
    .fi-dropdown-panel {
        border-radius: 0.75rem !important; /* rounded-xl */
    }

    .fi-btn {
        border-radius: 0.5rem !important; /* rounded-lg */
        font-weight: 500 !important;
        transition: all 0.15s ease-in-out !important;
    }

    .fi-input,
    .fi-select-input {
        border-radius: 0.5rem !important; /* rounded-lg */
    }

    /* Soft Modern Drop Shadows & Subtle Borders */
    .fi-section,
    .fi-wi-stats-overview-stat,
    .fi-ta-ctn {
        box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.05), 0 2px 4px -2px rgba(15, 23, 42, 0.05) !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
    }

    .dark .fi-section,
    .dark .fi-wi-stats-overview-stat,
    .dark .fi-ta-ctn {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -2px rgba(0, 0, 0, 0.25) !important;
        border: 1px solid rgba(51, 65, 85, 0.5) !important;
    }

    .fi-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.15), 0 2px 4px -2px rgba(37, 99, 235, 0.1);
    }

    .fi-btn:active {
        transform: translateY(0);
    }

    /* ==========================================================================
       WCAG 2.1 AA Accessibility & Visible Focus Indicators
       ========================================================================== */
    a:focus-visible,
    button:focus-visible,
    input:focus-visible,
    select:focus-visible,
    textarea:focus-visible,
    [tabindex]:focus-visible {
        outline: 2px solid #2563eb !important;
        outline-offset: 2px !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.25) !important;
    }

    .dark a:focus-visible,
    .dark button:focus-visible,
    .dark input:focus-visible,
    .dark select:focus-visible,
    .dark textarea:focus-visible,
    .dark [tabindex]:focus-visible {
        outline: 2px solid #60a5fa !important;
        outline-offset: 2px !important;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.3) !important;
    }

    /* Enhanced Contrast for Labels & Meta Text (WCAG 4.5:1 ratio) */
    .fi-fo-field-wrp-label label {
        font-weight: 600 !important;
        color: #1e293b !important;
    }

    .dark .fi-fo-field-wrp-label label {
        color: #f1f5f9 !important;
    }

    .fi-fo-field-wrp-helper-text {
        color: #475569 !important;
    }

    .dark .fi-fo-field-wrp-helper-text {
        color: #94a3b8 !important;
    }

    /* Mobile Responsiveness & Touch Target Sizing (Min 44px on mobile) */
    @media (max-width: 640px) {
        .fi-btn,
        .fi-input,
        .fi-select-input {
            min-height: 42px !important;
        }

        .fi-ta-ctn {
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
        }

        .fi-section-header {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }

        .fi-section-content {
            padding: 1rem !important;
        }
    }

    /* Livewire Loading Indicator Micro UX */
    [wire\:loading],
    [wire\:loading\.delay] {
        transition: opacity 0.2s ease-in-out;
    }
</style>
