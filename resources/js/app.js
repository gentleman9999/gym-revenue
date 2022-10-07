import AppLayout from "@/Layouts/AppLayout.vue";
import vClickOutside from "click-outside-vue3";

import "./bootstrap";

import { createApp, h, provide } from "vue";
import { createInertiaApp, Link, usePage } from "@inertiajs/inertia-vue3";
import { InertiaProgress } from "@inertiajs/progress";
import Toast from "vue-toastification";
import {
    ApolloClient,
    createHttpLink,
    InMemoryCache,
} from "@apollo/client/core";
import { DefaultApolloClient } from "@vue/apollo-composable";
import VueApolloComponents from "@vue/apollo-components";
import { createApolloProvider } from "@vue/apollo-option";

const appName =
    window.document.getElementsByTagName("title")[0]?.innerText || "Laravel";

const pageStore = usePage();

// HTTP connection to the API
const httpLink = createHttpLink({
    // You should use an absolute URL here
    uri: "http://localhost:8000/graphql",
    headers: {
        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
    },
});

// Cache implementation
const cache = new InMemoryCache();

// Create the apollo client
const apolloClient = new ApolloClient({
    link: httpLink,
    cache,
});

const apolloProvider = createApolloProvider({
    defaultClient: apolloClient,
});
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
        return createApp({
            setup() {
                provide(DefaultApolloClient, apolloClient);
            },
            render: () => h(app, props),
        })
            .use(plugin)
            .use(apolloProvider)
            .use(VueApolloComponents)
            .use(Toast)
            .use(vClickOutside)
            .component("inertia-link", Link)
            .mixin({ methods: { route } })
            .mount(el);
    },
});

InertiaProgress.init({ color: "#4B5563" });
