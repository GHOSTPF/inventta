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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            // Paleta da marca — troque aqui para redefinir a cor de destaque.
            colors: {
                brand: {
                    50: '#FFF5EC',
                    100: '#FFE8D3',
                    200: '#FFD0A8',
                    300: '#FFB37D',
                    400: '#FF8F40',
                    500: '#FF6900',
                    600: '#E65F00',
                    700: '#BF4E00',
                    800: '#993F00',
                    900: '#7A3300',
                },
                ink: {
                    DEFAULT: '#0F172A',
                    light: '#1E293B',
                },
            },
        },
    },

    plugins: [forms],
};
