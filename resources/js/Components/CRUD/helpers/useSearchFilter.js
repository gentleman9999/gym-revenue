import { ref, watch } from "vue";
import throttle from "lodash/throttle";
import { Inertia } from "@inertiajs/inertia";
import pickBy from "lodash/pickBy";
import mapValues from "lodash/mapValues";

/*
export const useSearchFilter = (baseRoute, initialState = {}) => {
    if(!baseRoute){
        throw 'Missing baseRoute';
    }

    const form = ref(initialState);

    const formHandler = throttle(function () {
        Inertia.get(route(baseRoute), pickBy(form.value), {
            preserveState: true,
            preserveScroll: true
        });
    }, 150);

    watch([form], formHandler, { deep: true });

    const reset = () => {
        form.value = mapValues(form.value, () => null);
    };

    return { form, reset };
};
*/

export const useSearchFilter = (baseRoute, initialState = {}, options = {
    preserveState: false,
    preserveScroll: true
}) => {
    if (!baseRoute) {
        throw 'Missing baseRoute';
    }

    const form = ref(initialState);

    const formHandler = throttle(function () {
        Inertia.get(route(baseRoute), pickBy(form.value), options);
    }, 150);

    watch([form], formHandler, {deep: true});

    const reset = () => {
        form.value = mapValues(form.value, () => null);
    };

    return {form, reset};
};


