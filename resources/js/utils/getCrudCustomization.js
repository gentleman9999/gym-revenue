import { usePage } from "@inertiajs/inertia-vue3";
import {computed, ref, watchEffect} from "vue";

export const getCrudConfig = (table) =>{
    const page = usePage();
    const config = computed(()=> page?.props.value.user?.column_config || {})
    //we compare json here to prevent double display of the alert
    if(table){
        return computed(()=>config.value[table] || []);
    }
    return config;
}
