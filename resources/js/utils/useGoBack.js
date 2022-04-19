import { usePage } from "@inertiajs/inertia-vue3";
import { computed } from "vue";
import { Inertia } from "@inertiajs/inertia";

export const useGoBack = (defaultUrl) => {
    const page = usePage();
    const previousUrl = computed(
        () => page?.props?.value?.previousUrl || defaultUrl
    );
    return () => Inertia.visit(previousUrl.value);
};
