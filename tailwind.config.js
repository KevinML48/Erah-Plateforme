import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/js/**/*.jsx',
        './resources/js/**/*.ts',
        './resources/js/**/*.tsx',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['system-ui', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                ui: {
                    bg: '#EEF1ED',
                    surface: '#F6F7F5',
                    panel: '#F1F3F0',
                    border: 'rgba(0,0,0,0.06)',
                    text: '#0B0B0F',
                    muted: 'rgba(11,11,15,0.55)',
                    accent: '#FF2A2A',
                    accentHover: '#FF4545',
                    accentSoft: 'rgba(255,42,42,0.16)',
                    sidebar: '#0B0B0F',
                },
            },
            borderRadius: {
                ui: '1rem',
                'ui-lg': '1.5rem',
            },
            boxShadow: {
                'ui-soft': '0 18px 42px rgba(0,0,0,0.12)',
            },
        },
    },

    plugins: [forms],
};
