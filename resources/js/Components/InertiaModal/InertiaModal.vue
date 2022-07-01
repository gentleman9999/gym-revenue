<!--
Hopefully will one day be solved by
https://github.com/inertiajs/inertia/pull/642
-->
<template>
    <template v-if="modal">
        <slot
            name="default"
            :loading="modal.loading"
            :component="modal.component"
            :page="modal.page"
            :close="modal.close"
            :props="modal.props"
        />
        <Teleport v-if="modal.component && telRef" :to="telRef">
            <Component is-modal :is="modal.component" v-bind="componentProps" />
        </Teleport>
    </template>
</template>

<script setup>
import { Inertia } from "@inertiajs/inertia";

import Axios from "axios";
import { provide, shallowRef, watch, computed, watchEffect, ref } from "vue";
import { fireErrorEvent, fireSuccessEvent } from "./events";
import uniqueId from "./uniqueId";
import {
    injectIsModal,
    modalHeader,
    modalRedirect,
    modalRedirectBack,
} from "./symbols";
import { provider } from "./useModalSlot";
import { useModal } from "@/Components/InertiaModal";

const props = defineProps({
    component: String,
    componentAttrs: Object,
    modalKey: {
        type: String,
        default: "",
    },
});

// const modal = shallowRef(null);
const modal = useModal();

const telRef = provider();

provide(injectIsModal, modal);

const close = async () => {
    console.log("Inertia Modal Close called", modal.value);
    if (modal.value) {
        console.log("entered block in InertiaModal::Close", modal.value);

        const shouldReloadOnClose =
            "reloadOnClose" in modal.value && modal.value.reloadOnClose;
        const hasOnClose = "onClose" in modal.value && modal.value.onClose;
        const modalValueBeforeClose = modal.value;
        reallyClose();

        if (shouldReloadOnClose) {
            console.log(
                "InertiaModal::close  - reloadOnClose set - about to Inertiareload"
            );
            //TODO: why is data not reloaded after calling Inertia.reload? you can see it reloading in the network tools
            Inertia.reload({
                onSuccess: () => {
                    if (hasOnClose) {
                        console.log(
                            "InertiaModal::close before calling onClose"
                        );
                        modal.value.onClose(modalValueBeforeClose);
                        console.log(
                            "InertiaModal::close after calling onClose"
                        );
                    }
                },
            });
            console.log("InertiaModal::close after calling InertiaReload");
        } else if (hasOnClose) {
            console.log("InertiaModal::close before calling onClose");
            modal.value.onClose(modalValueBeforeClose);
            console.log("InertiaModal::close after calling onClose");
        }
    }
};

const reallyClose = () => {
    if (modal.value) {
        console.log(
            "InertiaModal::close before dispatching custom event inertia:modal-closed"
        );
        if (!modal.value.loading) {
            // remove the 'x-inertia-modal' and 'x-inertia-modal-redirect-back' headers for future requests
            modal.value.removeBeforeEventListener();
            if (modal.value.removeSuccessEventListener) {
                modal.value.removeSuccessEventListener();
            }
            Axios.interceptors.response.eject(modal.value.interceptor);
        }

        if (modal.value.cancelToken.value) {
            console.log("InertiaModal::close::before cancel token cancelled");
            modal.value.cancelToken.value.cancel("Modal closed");
            console.log("InertiaModal::close::after cancel token cancelled");
        }
        document.dispatchEvent(
            new CustomEvent("inertia:modal-closed", { detail: modal.value })
        );
        console.log(
            "InertiaModal::close after dispatching custom event inertia:modal-closed"
        );
        modal.value = null;
    } else {
        console.error("tried to close modal but it was already closed");
    }
};

const visitInModal = (url, options = {}) => {
    const opts = {
        headers: {},
        redirectBack: false,
        modalProps: {},
        pageProps: {},
        reloadOnClose: true,
        redirectInModal: true,
        ...options,
    };
    const cancelToken = shallowRef(null);

    const hrefToUrl = (href) =>
        new URL(href.toString(), window.location.toString());

    const currentId = uniqueId();
    let lastPage;
    let lastVisit = null;

    const interceptor = Axios.interceptors.response.use((response) => {
        console.log("top level intercept entered");
        if (response.headers[modalHeader.toLowerCase()] !== currentId) {
            console.log("mismatch", {
                currentId,
                modalHeader: response.headers[modalHeader.toLowerCase()],
            });
        }
        if (response.headers[modalHeader.toLowerCase()] === currentId) {
            console.log(
                "reponse modal id = currentid",
                currentId,
                response.data
            );
            const page = response.data;
            page.url = hrefToUrl(page.url);
            console.log({ lastVisit, lastPage });
            if (
                lastVisit?.only &&
                lastPage &&
                lastPage.component === page.component
            ) {
                console.log("lastVisit.only set - do some hacky shit");
                page.props = { ...lastPage.props, ...page.props };
            }
            if (lastPage && lastPage.component === page.component) {
                console.log("maybe this means we set the error bags?");
                page.props = { ...lastPage.props, ...page.props };
            }

            console.log({ "inertia.activeVisit": Inertia.activeVisit });
            const { onError, onSuccess, errorBag } = lastVisit
                ? Inertia.activeVisit
                : opts;
            console.log({ onError, onSuccess, errorBag });
            const errors = page.props.errors || {};
            let component = page.component;
            if (Object.keys(errors).length) {
                component = lastPage.component;
            }

            Promise.resolve(Inertia.resolveComponent(component))
                .then((component) => {
                    console.log("resolved an inertia component", page);

                    // const errors = page.props.errors || {};
                    console.log({ errors, errorBag });
                    if (Object.keys(errors).length) {
                        const scopedErrors =
                            errorBag != "" ? errors[errorBag] || {} : errors;
                        console.log(
                            "before fire error event",
                            scopedErrors,
                            errors
                        );
                        fireErrorEvent(scopedErrors);
                        console.log("after fire error event");
                        if (onError) onError(scopedErrors);
                    } else {
                        console.log("before fire success event");
                        fireSuccessEvent(page);
                        console.log("after fire success event");
                        if (onSuccess) onSuccess(page);
                    }
                    return component;
                })
                .then((component) => {
                    Inertia.finishVisit(Inertia.activeVisit);
                    let removeSuccessEventListener;
                    if (
                        modal.value &&
                        "removeBeforeEventListener" in modal.value
                    ) {
                        console.log("removing inertia before event listerener");
                        modal.value.removeBeforeEventListener();
                    }
                    const removeBeforeEventListener = Inertia.on(
                        "before",
                        (event) => {
                            console.log("onBefore", { event, opts });
                            // Subsequent visit of the modal url will stay in the modal
                            if (
                                event.detail.visit.url.pathname ===
                                page.url.pathname
                            ) {
                                console.log(
                                    "[inertia-modal] event.detail.visit.url.pathname = page.url.pathname"
                                );
                                // make sure the backend knows we're requesting from within a modal
                                event.detail.visit.headers[modalHeader] =
                                    currentId;
                                lastVisit = event.detail.visit;
                                lastPage = page;
                                const reqInterceptor =
                                    Axios.interceptors.request.use((config) => {
                                        console.log(
                                            "setting up axios interceoptor in default interceptor",
                                            config
                                        );

                                        if (
                                            config.headers[modalHeader] ===
                                            currentId
                                        ) {
                                            console.log(
                                                "config.headers[modalHeader] === currentId"
                                            );
                                            Axios.interceptors.request.eject(
                                                reqInterceptor
                                            );
                                            config.headers[
                                                "X-Inertia-Partial-Component"
                                            ] = page.component;
                                        } else {
                                            console.log(
                                                "config.headers[modalHeader] !== currentId",
                                                {
                                                    currentId,
                                                    headers: config.headers,
                                                }
                                            );
                                        }
                                        return config;
                                    });
                            } else if (
                                opts.redirectBack ||
                                modalRedirectBack in event.detail.visit.headers
                            ) {
                                const redirectBackVal =
                                    opts.redirectBack ||
                                    event.detail.visit.headers[
                                        modalRedirectBack
                                    ];
                                console.log(
                                    "[inertia-modal] visitInModal = opts.redirectBack===true"
                                );
                                lastVisit = event.detail.visit;
                                lastPage = page;
                                event.detail.visit.headers[modalHeader] =
                                    currentId;
                                event.detail.visit.headers[modalRedirectBack] =
                                    "true";
                                event.detail.visit.headers[
                                    "X-Inertia-Partial-Component"
                                ] = page.component;
                                const reqInterceptor =
                                    Axios.interceptors.request.use((config) => {
                                        console.log(
                                            "setting up axios interceoptor in default interceptor",
                                            config
                                        );

                                        if (
                                            config.headers[modalHeader] ===
                                            currentId
                                        ) {
                                            console.log(
                                                "config.headers[modalHeader] === currentId"
                                            );
                                            Axios.interceptors.request.eject(
                                                reqInterceptor
                                            );
                                            config.headers[
                                                "X-Inertia-Partial-Component"
                                            ] = page.component;
                                        } else {
                                            console.log(
                                                "config.headers[modalHeader] !== currentId",
                                                {
                                                    currentId,
                                                    headers: config.headers,
                                                }
                                            );
                                        }
                                        return config;
                                    });

                                if (typeof opts.redirectBack === "function") {
                                    removeSuccessEventListener = Inertia.on(
                                        "success",
                                        opts.redirectBack
                                    );
                                }
                                if (redirectBackVal === "close") {
                                    console.log(
                                        "[inertia-modal] redirect back set to CLOSE"
                                    );
                                    removeSuccessEventListener = Inertia.on(
                                        "success",
                                        () => modal.value?.close()
                                    );
                                }
                            } else if (
                                opts.redirectInModal ||
                                modalRedirect in event.detail.visit.headers
                            ) {
                                console.log(
                                    "[inertia-modal] modalRedirect in event.detail.visit.headers"
                                );
                                //check if we wanted a redirect in the modal
                                event.detail.visit.headers[modalHeader] =
                                    currentId;
                                lastVisit = event.detail.visit;
                                lastPage = page;
                                const reqInterceptor =
                                    Axios.interceptors.request.use((config) => {
                                        console.log(
                                            "setting up axios interceoptor in modalRedirect",
                                            config
                                        );
                                        if (
                                            config.headers[modalHeader] ===
                                            currentId
                                        ) {
                                            Axios.interceptors.request.eject(
                                                reqInterceptor
                                            );
                                            config.headers[
                                                "X-Inertia-Partial-Component"
                                            ] = page.component;
                                        }
                                        return config;
                                    });
                            } else {
                                console.log("[inertia-modal] no match,", event);
                            }
                        }
                    );
                    modal.value = {
                        loading: false,
                        component,
                        removeBeforeEventListener,
                        removeSuccessEventListener,
                        interceptor,
                        page,
                        cancelToken,
                        onClose: opts.onClose,
                        reloadOnClose: opts.reloadOnClose,
                        props: opts.modalProps,
                        pageProps: opts.pageProps,
                        lastPage,
                        close,
                    };
                });
            console.log("before rejecting prmoise with axios cancel");
            return Promise.reject(new axios.Cancel());
        }
        return response;
    });
    console.log("Calling Inertia.Visit from IneritaModal:visitInModal", {
        url,
        opts,
    });

    Inertia.visit(url, {
        ...opts,
        onCancelToken: (token) => {
            cancelToken.value = token;
        },
        headers: { ...opts.headers, [modalHeader]: currentId },
    });
    modal.value = { loading: true, cancelToken, close };
};

watch(
    () => props.modalKey,
    (key, oldKey) => {
        if (key !== oldKey && !Inertia[`visitInModal${key}`]) {
            console.log("setting up VisitInModal", { key, oldKey });
            const fn = `visitInModal${key}`;
            Inertia[fn] = visitInModal;
        }
    },
    { immediate: true }
);
const componentProps = computed(() => {
    const hasErrors = !!Object.keys(modal.value?.page?.props?.errors).length;

    if (hasErrors) {
        const modalLastPageProps = modal.value?.lastPage?.props || {};
        return { ...modalLastPageProps, ...modal.value.pageProps };
    }
    const modalPageProps = modal.value?.page?.props || {};
    return { ...modalPageProps, ...modal.value.pageProps };
});
</script>

<script>
export default {
    name: "InertiaModal",
};
</script>
