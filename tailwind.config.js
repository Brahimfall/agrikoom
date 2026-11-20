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
            },
            colors: {
                brand: {
                    50:  '#f7f2f0',
                    100: '#efe5e1',
                    200: '#deccC4',
                    300: '#c4a99c',
                    400: '#a88474',
                    500: '#7D5A50',
                    600: '#6b4d44',
                    700: '#5a3f38',
                    800: '#4a342e',
                    900: '#3d2b26',
                    950: '#2a1c19',
                },
            },
        },
    },

    plugins: [forms],
};
