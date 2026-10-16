import preset from './vendor/filament/support/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#e8f0fe',
                    100: '#d2e3fc',
                    200: '#aecbfa',
                    300: '#8ab4f8',
                    400: '#669df6',
                    500: '#1a73e8', // Google Blue
                    600: '#1967d2',
                    700: '#185abc',
                    800: '#174ea6',
                    900: '#0d2758',
                    950: '#061738',
                },
                slate: {
                    50: '#f8fafd',
                    100: '#f1f3f4',
                    200: '#e0e4e9',
                    300: '#cbd5e1',
                    400: '#9aa0a6',
                    500: '#5f6368',
                    600: '#475569',
                    700: '#37393b',
                    800: '#1e1f20',
                    900: '#131314',
                    950: '#0d0e0f',
                },
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            borderRadius: {
                card: '0.75rem',    // rounded-xl
                btn: '0.5rem',      // rounded-lg
                input: '0.5rem',    // rounded-lg
            },
            boxShadow: {
                'soft-sm': '0 1px 3px 0 rgba(15, 23, 42, 0.05), 0 1px 2px -1px rgba(15, 23, 42, 0.05)',
                'soft': '0 4px 6px -1px rgba(15, 23, 42, 0.06), 0 2px 4px -2px rgba(15, 23, 42, 0.06)',
                'soft-md': '0 10px 15px -3px rgba(15, 23, 42, 0.07), 0 4px 6px -4px rgba(15, 23, 42, 0.07)',
                'soft-lg': '0 20px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.08)',
            },
        },
    },
}
