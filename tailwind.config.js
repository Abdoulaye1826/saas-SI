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
                // Illustration manette du hero : flottement lent, jamais de
                // rotation rapide (cahier des charges §3 : "premium, lent").
                'float-slow': {
                    '0%, 100%': { transform: 'translateY(0) rotate(-2deg)' },
                    '50%': { transform: 'translateY(-14px) rotate(1deg)' },
                },
                // Particules d'arrière-plan (hero, bandeau promo).
                'particle-float': {
                    '0%': { transform: 'translateY(0) translateX(0)', opacity: '0' },
                    '10%': { opacity: 'var(--particle-opacity, .6)' },
                    '90%': { opacity: 'var(--particle-opacity, .6)' },
                    '100%': { transform: 'translateY(-120px) translateX(var(--particle-drift, 12px))', opacity: '0' },
                },
                // Pulsation très douce d'un halo/bordure lumineuse.
                'glow-pulse': {
                    '0%, 100%': { opacity: '.55' },
                    '50%': { opacity: '1' },
                },
                // Balayage scanline horizontal, très discret.
                'scan': {
                    '0%': { transform: 'translateY(-100%)' },
                    '100%': { transform: 'translateY(100%)' },
                },
            },
            animation: {
                'fade-up': 'fade-up .6s cubic-bezier(.16,1,.3,1) both',
                'pop': 'pop .4s cubic-bezier(.34,1.56,.64,1)',
                'drift': 'drift 14s ease-in-out infinite',
                'float-slow': 'float-slow 7s ease-in-out infinite',
                'particle-float': 'particle-float linear infinite',
                'glow-pulse': 'glow-pulse 3.5s ease-in-out infinite',
                'scan': 'scan 6s linear infinite',
            },
        },
    },

    plugins: [forms],
};
