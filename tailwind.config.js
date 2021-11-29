const defaultTheme = require('tailwindcss/defaultTheme');
const plugin = require('tailwindcss/plugin');

const twColors = require('tailwindcss/colors');
//add default to colors so we don't always need -500 for base shade
Object.entries(twColors).forEach(([name, color]) => color.DEFAULT = color[500]);

const colors = {
    ...twColors,
    primary: {
        DEFAULT: '#073A76',
        '50': '#98C4F9',
        '100': '#7BB4F7',
        '200': '#4193F4',
        '300': '#0E73EA',
        '400': '#0A56B0',
        '500': '#073A76',
        '600': '#052C59',
        '700': '#041E3C',
        '800': '#020F1F',
        '900': '#000102'
    },
    secondary: {
        DEFAULT: '#0074C8',
        '50': '#AFDDFF',
        '100': '#95D2FF',
        '200': '#62BDFF',
        '300': '#2FA8FF',
        '400': '#0092FB',
        '500': '#0074C8',
        '600': '#00599A',
        '700': '#003F6C',
        '800': '#00243E',
        '900': '#000A10'
    },
    accent: {
        DEFAULT: '#82D300',
        '50': '#D3FF8C',
        '100': '#CBFF77',
        '200': '#BBFF4E',
        '300': '#ACFF26',
        '400': '#9BFC00',
        '500': '#82D300',
        '600': '#6CAF00',
        '700': '#568C00',
        '800': '#406800',
        '900': '#2A4400'
    }
}

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
            colors,
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            fontSize:{
                '2xs': ['0.6rem', { lineHeight: '0.75rem' }],
            }
        },
    },

    plugins: [
        // require('@tailwindcss/forms'),
        // require('@tailwindcss/typography'),
        require('daisyui'),
        plugin(({addUtilities}) => {
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
                'primary': colors.primary[500],
                'primary-focus': colors.primary[400],
                'primary-content': '#ffffff',
                'secondary': colors.secondary[500],
                'secondary-focus': colors.secondary[600],
                'secondary-content': '#ffffff',
                'accent': colors.accent[500],
                'accent-focus': colors.accent[600],
                'accent-content': '#ffffff',
                'neutral': '#1c1c1c',
                'neutral-focus': colors.primary[500],
                'neutral-content': '#ffffff',
                'base-100': '#242424',
                'base-200': '#202020',
                'base-300': '#1c1c1c',
                // 'base-100': '#232D38',
                // 'base-200': '#192028',
                // 'base-300': '#0F1318',
                'base-content': '#ffffff',
                'info': '#2094f3',
                'success': '#01B481',
                'warning': '#ff9d00',
                'error': '#ff0000',
            },
        }]
    },
};
