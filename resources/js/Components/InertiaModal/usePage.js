import { usePage } from "@inertiajs/inertia-vue3";
import { computed } from "vue";
import isModal from "./isModal";

export default () => {
    const modal = isModal();
    const parent = usePage();
    if (modal && modal.value) {
        return {
            isModal: true,
            parent,
            props: computed(() =>
                modal.value && !modal.value.loading
                    ? modal.value.page.props
                    : {}
            ),
            url: computed(
                () =>
                    modal.value && !modal.value.loading && modal.value.page.url
            ),
            component: computed(
                () =>
                    modal.value &&
                    !modal.value.loading &&
                    modal.value.page.component
            ),
            version: computed(
                () =>
                    modal.value &&
                    !modal.value.loading &&
                    modal.value.page.version
            ),
        };
    }
    return parent;
};
