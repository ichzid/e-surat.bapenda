import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                navy: {
                    DEFAULT: '#0F172A',
                    dark: '#020617',
                    hover: '#0F172A0A',
                    border: '#E2E8F0',
                },
                slate: {
                    DEFAULT: '#64748B',
                    secondary: '#475569',
                    bg: '#F1F5F9',
                },
                sage: {
                    DEFAULT: '#059669',
                },
                surface: {
                    DEFAULT: '#FFFFFF',
                    bg: '#F8FAFC',
                },
                status: {
                    success: '#22C55E',
                    warning: '#EAB308',
                    error: '#EF4444',
                    info: '#0EA5E9',
                }
            },
            fontFamily: {
                sans: ['Outfit', ...defaultTheme.fontFamily.sans],
                display: ['Outfit', ...defaultTheme.fontFamily.sans],
                mono: ['Fira Code', ...defaultTheme.fontFamily.mono],
            },
            boxShadow: {
                'sm': '0 1px 3px 0 rgba(15, 23, 42, 0.03)',
                'DEFAULT': '0 2px 6px 0 rgba(15, 23, 42, 0.05)',
                'md': '0 4px 16px 0 rgba(15, 23, 42, 0.07)',
                'lg': '0 8px 32px 0 rgba(15, 23, 42, 0.10)',
            },
        },
    },

    plugins: [forms, typography],
};
