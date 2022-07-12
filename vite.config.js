import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import fs from 'fs';
import laravel from 'laravel-vite-plugin'
import {homedir} from 'os'
import path, {resolve} from 'path'
import { ViteWebfontDownload } from 'vite-plugin-webfont-dl';

const host = process.env.APP_URL;

export default defineConfig({
    plugins: [
        laravel([
            'resources/css/app.css',
            'resources/js/app.js',
        ]),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        {
            name: 'blade',
            handleHotUpdate({ file, server }) {
                if (file.endsWith('.blade.php')) {
                    server.ws.send({
                        type: 'full-reload',
                        path: '*',
                    });
                }
            },
        },
        ViteWebfontDownload(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
            'axios': path.resolve('node_modules/axios/dist/axios.js'),
            'tailwind.config.js': path.resolve(__dirname, 'tailwind.config.js'),
        }
    },
    optimizeDeps: {
        include: [
            'tailwind.config.js',
        ]
    },
    build: {
        commonjsOptions: {
            include: ['tailwind.config.js', 'node_modules/**'],
        },
    },
    server: detectServerConfig(host),
    css:{
        devSourcemap: true
    }
})

function detectServerConfig(host) {
    let keyPath = resolve(homedir(), `.config/valet/Certificates/${host}.key`)
    let certificatePath = resolve(homedir(), `.config/valet/Certificates/${host}.crt`)

    if (!fs.existsSync(keyPath)) {
        return {}
    }

    if (!fs.existsSync(certificatePath)) {
        return {}
    }

    return {
        hmr: {host},
        host,
        https: {
            key: fs.readFileSync(keyPath),
            cert: fs.readFileSync(certificatePath),
        },
    }
}

//If we ever need client-side image optimization:
//https://github.com/ElMassimo/vite-plugin-image-presets
