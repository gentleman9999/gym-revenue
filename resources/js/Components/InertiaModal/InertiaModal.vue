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
            <Component
                is-modal
                :is="modal.component"
                v-bind="{ ...modal.page.props, ...modal.pageProps }"
            />
        </Teleport>
    </template>
</template>

<script setup>
import { Inertia } from "@inertiajs/inertia";
import Axios from "axios";
import { provide, shallowRef, watch, watchEffect } from "vue";
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

const close = () => {
    console.log("Inertia Modal Close called", modal.value);
    if (modal.value) {
        console.log("entered block in InertiaModal::Close", modal.value);
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
        if ("onClose" in modal.value && modal.value.onClose) {
            console.log("InertiaModal::close before calling onClose");
            modal.value.onClose(modal.value);
            console.log("InertiaModal::close after calling onClose");
        }
        if ("reloadOnClose" in modal.value && modal.value.reloadOnClose) {
            console.log(
                "InertiaModal::close  - reloadOnClose set - about to Inertiareload"
            );
            Inertia.reload();
            console.log("InertiaModal::close after calling InertiaReload");
        }
    }
    console.log(
        "InertiaModal::close before dispatching custom event inertia:modal-closed"
    );
    document.dispatchEvent(
        new CustomEvent("inertia:modal-closed", { detail: modal.value })
    );
    console.log(
        "InertiaModal::close after dispatching custom event inertia:modal-closed"
    );
    modal.value = null;
};

const visitInModal = (url, options = {}) => {
    const opts = {
        headers: {},
        redirectBack: false,
        modalProps: {},
        pageProps: {},
        reloadOnClose: false,
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
        if (response.headers[modalHeader.toLowerCase()] === currentId) {
            console.log("reponse modal id = currentid", currentId);
            const page = response.data;
            page.url = hrefToUrl(page.url);
            if (
                lastVisit?.only &&
                lastPage &&
                lastPage.component === page.component
            ) {
                page.props = { ...lastPage.props, ...page.props };
            }

            const { onError, onSuccess, errorBag } = lastVisit
                ? Inertia.activeVisit
                : opts;

            Promise.resolve(Inertia.resolveComponent(page.component))
                .then((component) => {
                    console.log("resolved an inertia component", component);

                    const errors = page.props.errors || {};
                    if (Object.keys(errors).length > 0) {
                        const scopedErrors = errorBag
                            ? errors[errorBag] || {}
                            : errors;
                        console.log("before fire error event");
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
                            // Subsequent visit of the modal url will stay in the modal
                            if (
                                event.detail.visit.url.pathname ===
                                page.url.pathname
                            ) {
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
                                            Axios.interceptors.request.eject(
                                                reqInterceptor
                                            );
                                            config.headers[
                                                "X-Inertia-Partial-Component"
                                            ] = page.component;
                                        }
                                        return config;
                                    });
                            } else if (
                                modalRedirect in event.detail.visit.headers
                            ) {
                                console.log(
                                    "modalRedirect in event.detail.visit.headers"
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
                            } else if (opts.redirectBack) {
                                console.log(
                                    "visitInModal = opts.redirectBack===true"
                                );
                                event.detail.visit.headers[modalRedirectBack] =
                                    "true";
                                if (typeof opts.redirectBack === "function") {
                                    removeSuccessEventListener = Inertia.on(
                                        "success",
                                        opts.redirectBack
                                    );
                                }
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
                        close,
                    };
                });
            console.log("before rejecting prmoise with axios cancel");
            return Promise.reject(new axios.Cancel());
        }
        return response;
    });
    console.log("Calling Inertia.Visit from IneritaModal:visitInModal");

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
    (key) => {
        console.log("setting up VisitInModal");
        const fn = `visitInModal${key}`;
        Inertia[fn] = visitInModal;
    },
    { immediate: true }
);
</script>

<script>
export default {
    name: "InertiaModal",
};
</script>
