import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/**/*.blade.php',
        './resources/**/**/**/*.blade.php',
        './resources/**/**/**/**/*.blade.php',
        './resources/**/**/**/**/**/*.blade.php',
        './resources/views/filament/pages/*.blade.php',
        './resources/views/filament/widgets/*.blade.php',
        './resources/views/vendor/filament-kanban/*.blade.php',
        './resources/views/filament/resources/thanks-dairy-resource/pages/*.blade.php',
        './resources/**/**/**/**/**/**/*.blade.php',
        './vendor/guava/calendar/resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [],
};
