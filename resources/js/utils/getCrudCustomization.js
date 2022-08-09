import { usePage } from "@inertiajs/inertia-vue3";
import { computed, ref, watchEffect } from "vue";

export const getCrudConfig = (table) => {
    if (!table.endsWith("s")) {
        table = `${table}s`;
    }
    const page = usePage();
    const config = computed(() => page?.props.value.user?.column_config || {});
    console.log({ config: config.value, table });
    //we compare json here to prevent double display of the alert
    if (table) {
        console.log(config.value["misc"]);
        return computed(() => config.value[table] || []);
    }
    return config;
};
