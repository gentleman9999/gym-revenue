const path = require('path');

module.exports = {
    resolve: {
        alias: {
            '@': path.resolve('resources/js'),
            'axios': path.resolve('node_modules/axios/dist/axios.js'),
        },
    },
    externals: {
        // only define the dependencies you are NOT using as externals!
        canvg: "canvg",
        html2canvas: "html2canvas",
        dompurify: "dompurify"
    },
    experiments: {
        topLevelAwait: true,
    }
};
