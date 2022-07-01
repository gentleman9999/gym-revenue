import { useForm } from "@inertiajs/inertia-vue3";
import { computed, watch } from "vue";
import cloneDeep from "lodash.clonedeep"; //TODO:figure out why using lodash/clonedeep breaks on dev
import omitBy from "lodash/omitBy";
import { useModalPage } from "@/Components/InertiaModal";

/**
 * Creates an enhanced version of inertia-vue3's useForm helper.
 * The form object returned has a "dirty" prpoerty, which is
 * @param args
 * @returns {any}
 */
export const useGymRevForm = (...args) => {
    const form = useForm(...args);
    const page = useModalPage();
    watch(
        () => page.props.value.errors,
        (newErrors) => {
            console.log("detected change in page", newErrors);
            console.log(
                "detected errors on modal version of the form. setting here."
            );
            form.errors = newErrors;
        }
    );
    console.log({ page });

    const data = (typeof args[0] === "string" ? args[1] : args[0]) || {};

    //immutable copy of initial form data
    const initialData = cloneDeep(data);
    Object.freeze(initialData);

    //TODO: Using initialData, and the form object, create a "dirtyFields" property,
    //TODO: which is an Object with the keys from initialData, and boolean if the value
    //TODO: is dirty or not.  You could also use a ref + watch instead of computed.
    const dirtyFields = computed(() => {
        let fields = [];
        for (let key in form) {
            if (initialData[key] !== form[key]) fields.push(key);
        }
        return fields;
    });

    /**
     * transform function that removes any properties that aren't dirty.
     */
    const onlyDirty = (data) => {
        let ret = omitBy(data, (value, key) => {
            return !dirtyFields.value?.includes(key);
        });
        return ret;
    };

    const dirty = () => {
        let upgraded = onlyDirty({ ...form });
        return upgraded;
    };
    form.dirty = dirty;
    return form;
};
