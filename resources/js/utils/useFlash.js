import { usePage } from "@inertiajs/inertia-vue3";
import {computed, ref, watchEffect} from "vue";

export const useFlash = () =>{
    const page = usePage();
    const flash = ref(page.props?.value?.flash);
    //we compare json here to prevent double display of the alert

    watchEffect(()=>{
        const newFlash = page.props?.value?.flash
        if(JSON.stringify(flash.value) !== JSON.stringify(newFlash)){
            // console.log({oldFlash: JSON.stringify(flash.value), newFlash: JSON.stringify(newFlash)})
            flash.value=newFlash;
        }
    })
    return flash;
}
