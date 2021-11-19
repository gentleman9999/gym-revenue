const defaultTheme = require('tailwindcss/defaultTheme');
const plugin = require('tailwindcss/plugin');

// const colors = require('tailwindcss/colors');
//add default to colors so we don't always need -500 for base shade
// Object.entries(colors).forEach(([name, color]) => color.DEFAULT = color[500]);

module.exports = {
    dark: false,
    experimental: {
        applyComplexClasses: true,
    },
    mode: 'jit',
    purge: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    safelist: [
        /data-theme$/,
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        // require('@tailwindcss/forms'),
        // require('@tailwindcss/typography'),
        require('daisyui'),
        plugin(function({ addUtilities }) {
            const newUtilities = {
                '.position-unset': {
                    position: 'unset',
                },
            }
            addUtilities(newUtilities)
        })
    ],
    daisyui: {
        themes: [{
            dark: {
                'primary': '#073a76',
                'primary-focus': '#0154ad',
                'primary-content': '#ffffff',
                'secondary': '#0074C8',
                'secondary-focus': '#0092fe',
                'secondary-content': '#ffffff',
                'accent': '#82D300',
                'accent-focus': '#99f800',
                'accent-content': '#ffffff',
                'neutral': '#1c1c1c',
                'neutral-focus': '#073a76',
                'neutral-content': '#ffffff',
                'base-100': '#242424',
                'base-200': '#202020',
                'base-300': '#1c1c1c',
                'base-content': '#ffffff',
                'info': '#2094f3',
                'success': '#01B481',
                'warning': '#ff9d00',
                'error': '#ff0000',
            },
        }]
    },
};
