module.exports = {
    plugins: {
        'postcss-import': {},
        'tailwindcss/nesting': {},
        tailwindcss: {},
        autoprefixer: {},
        //TODO: optimize via cssnano only on prod build
        // require('cssnano')({
        //     "preset": [
        //         "default",
        //         {
        //             "mergeRules": false
        //         }
        //     ]
        // }),
    }
}
