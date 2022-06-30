import AppLayout from "@/Layouts/AppLayout.vue";

import "./bootstrap";

import { createApp, h } from "vue";
import { createInertiaApp, Link, usePage } from "@inertiajs/inertia-vue3";
import { InertiaProgress } from "@inertiajs/progress";
import Toast from "vue-toastification";

const appName =
    window.document.getElementsByTagName("title")[0]?.innerText || "Laravel";

const pageStore = usePage();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: async (name) => {
        const module = await import(`./Pages/${name}.vue`);
        const page = module.default;
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
    },
    setup({ el, app, props, plugin }) {
        return createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(Toast)
            .component("inertia-link", Link)
            .mixin({ methods: { route } })
            .mount(el);
    },
});

InertiaProgress.init({ color: "#4B5563" });
