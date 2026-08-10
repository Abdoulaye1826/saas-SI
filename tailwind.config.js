import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                // Boutique en ligne uniquement : titres/prix en Space Grotesk
                // (voir resources/views/layouts/storefront.blade.php) — le
                // back-office garde Bootstrap/Figtree tel quel.
                display: ['"Space Grotesk"', ...defaultTheme.fontFamily.sans],
            },
            keyframes: {
                'fade-up': {
                    '0%': { opacity: '0', transform: 'translateY(16px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'pop': {
                    '0%': { transform: 'scale(1)' },
                    '50%': { transform: 'scale(1.18)' },
                    '100%': { transform: 'scale(1)' },
                },
                'drift': {
                    '0%, 100%': { transform: 'translate(0, 0) scale(1)' },
                    '50%': { transform: 'translate(-3%, 3%) scale(1.05)' },
                },
            },
            animation: {
                'fade-up': 'fade-up .6s cubic-bezier(.16,1,.3,1) both',
                'pop': 'pop .4s cubic-bezier(.34,1.56,.64,1)',
                'drift': 'drift 14s ease-in-out infinite',
            },
        },
    },

    plugins: [forms],
};
