import { usePage } from "@inertiajs/inertia-vue3";
import { computed } from "vue";

export const useCsrfToken = () => {
    const page = usePage();
    return computed(() => {
        return page?.props?.value?.user?.csrf_token;
    });
};
