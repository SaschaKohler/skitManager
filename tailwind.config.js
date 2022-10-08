const colors = require('tailwindcss/colors')
const plugin = require('tailwindcss/plugin');
const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        './resources/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                gray: colors.slate,
                danger: colors.rose,
                primary: colors.blue,
                success: colors.green,
                warning: colors.orange,
                purple: colors.purple,
                fuchsia: colors.fuchsia,
            },
            fontFamily: {
                sans: ['DM Sans', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        // plugin(function({ addComponents }) {
        //     addComponents({
        //         '.filament-button,.filament-tables-container': {
        //             borderRadius: '0 !important'
        //         },
        //         // '.filament-sidebar-item > a' :{
        //         //     borderRadius: '0 !important'
        //         // },
        //         '.filament-tables-pagination div' :{
        //             borderRadius: '0 !important'
        //         },
        //         '.ring-2' : {
        //             borderRadius: '0 !important'
        //         },
        //         'input,select' :{
        //             borderRadius: '0 !important'
        //         }
        //     })
        // })
    ],
}
