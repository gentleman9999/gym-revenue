import AppLayout from "@/Layouts/AppLayout.vue";
import vClickOutside from "click-outside-vue3";

import "./bootstrap";

import { createApp, h } from "vue";
import { createInertiaApp, Link, usePage } from "@inertiajs/inertia-vue3";
import { InertiaProgress } from "@inertiajs/progress";
import Toast from "vue-toastification";
import * as Sentry from "@sentry/browser";
import { BrowserTracing } from "@sentry/tracing";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

const appName =
    window.document.getElementsByTagName("title")[0]?.innerText || "Laravel";

const pageStore = usePage();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: async (name) => {
        const comps = import.meta.glob("./Pages/**/*.vue");
        const match = comps[`./Pages/${name}.vue`];
        try {
            const page = (await match()).default;
            // const module = await import(`./Pages/${name}.vue`);
            // const page = module.default;
            console.log({ name, page });
            if (page.layout === undefined) {
                if (name.startsWith("Invite/Show")) {
                    if (pageStore?.props?.value?.user) {
                        page.layout = AppLayout;
                    }
                } else {
                    page.layout = AppLayout;
                }
            }

            return page;
        } catch (e) {
            console.error("Could not load Page component", name);
            throw e;
        }
    },
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(Toast)
            .use(vClickOutside)
            .component("inertia-link", Link)
            .component("font-awesome-icon", FontAwesomeIcon)
            .mixin({ methods: { route } })
            .mount(el);
    },
});

InertiaProgress.init({ color: "#4B5563" });
Sentry.init({
    dsn: import.meta.env.VITE_SENTRY_LARAVEL_DSN,

    // Alternatively, use `process.env.npm_package_version` for a dynamic release version
    // if your build tool supports it.
    release: import.meta.env.VITE_SENTRY_RELEASE,
    integrations: [new BrowserTracing()],

    // Set tracesSampleRate to 1.0 to capture 100%
    // of transactions for performance monitoring.
    // We recommend adjusting this value in production
    tracesSampleRate: import.meta.env.VITE_SENTRY_TRACES_SAMPLE_RATE,
});
