import { useForm } from "@inertiajs/inertia-vue3";
import { computed, watch, ref } from "vue";
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
            console.log(
                "detected errors on modal version of the form. setting here.",
                newErrors
            );
            form.errors = newErrors;
        }
    );

    const initialData = ref(JSON.parse(JSON.stringify(args[0]))); //deep cloning args to prevent reference issue.
    watch(form, () => {
        if (form.recentlySuccessful) {
            initialData.value = form.data();
        }
    });
    const currentData = computed(() => form.data());

    const dirtyFields = computed(() => {
        console.log({ initialData: initialData.value });
        let fields = [];
        for (let key in initialData.value) {
            // console.log({key, initial: initialData.value[key], current: currentData.value[key] })
            const initialValue = initialData.value[key];
            const currentValue = currentData.value[key];
            if (typeof initialValue === "Object") {
                if (
                    JSON.stringify(initialValue) !==
                    JSON.stringify(currentValue)
                ) {
                    console.log("found diff", key);
                    fields.push(key);
                    break;
                }
            } else if (initialValue !== currentValue) {
                console.log("found diff", key);
                fields.push(key);
            }
        }
        return fields;
    });

    const dirtyData = computed(() =>
        Object.keys(currentData.value).reduce((carry, key) => {
            if (dirtyFields.value.includes(key)) {
                carry[key] = currentData.value[key];
            }
            return carry;
        }, {})
    );

    watch(dirtyFields, (a) => console.log({ dirtyFields: a }));
    watch(dirtyData, (a) => console.log({ dirtyData: a }));
    watch(currentData, (a) => console.log({ currentData: a }));

    /**
     * transform function that removes any properties that aren't dirty.
     */
    const onlyDirty = (data) => {
        let dirtyData = omitBy(data, (value, key) => {
            return !dirtyFields.value?.includes(key);
        });
        return dirtyData;
    };

    const dirty = () => {
        const ogTransform = form.transform;
        form.transform = (callback) => {
            ogTransform((data) => {
                const dirtyData = onlyDirty(data);
                return callback(dirtyData);
            });
            return form;
        };
        ogTransform(onlyDirty);

        return form;
    };

    form.dirty = dirty;

    return form;
};
