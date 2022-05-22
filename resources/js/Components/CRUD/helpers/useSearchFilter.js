import { ref, watch } from "vue";
import throttle from "lodash/throttle";
import { Inertia } from "@inertiajs/inertia";
import pickBy from "lodash/pickBy";
import mapValues from "lodash/mapValues";

export const useSearchFilter = (
    baseRoute,
    initialState = {
        sort: null,
        search: null,
    },
    options = {
        preserveState: true,
        preserveScroll: true,
    }
) => {
    if (!baseRoute) {
        throw "Missing baseRoute";
    }

    const urlSearchParams = new URLSearchParams(window.location.search);
    const search = paramsToObject(urlSearchParams);

    const form = ref({ ...initialState, ...search });

    const formHandler = throttle(function () {
        console.log("formchange", {
            form: form.value,
            pickBy: pickBy(form.value),
        });
        Inertia.get(route(baseRoute), pickBy(form.value), options);
    }, 150);

    watch([form], formHandler, { deep: true });

    const reset = () => {
        form.value = mapValues(form.value, () => null);
    };

    const clearFilters = () => {
        const { search, ...filters } = form.value;
        form.value = { ...mapValues(filters, () => null), search };
    };

    const clearSearch = () => {
        form.value.search = null;
    };

    return { form, reset, clearFilters, clearSearch };
};

const paramsToObject = (entries) => {
    const result = {};
    for (let [key, value] of entries) {
        // each 'entry' is a [key, value] tupple
        if (key.endsWith("[]")) {
            key = key.replace("[]", "");
            if (result[key]) {
                result[key].push(value);
            } else {
                result[key] = [value];
            }
        } else {
            result[key] = value;
        }
    }
    return result;
};
