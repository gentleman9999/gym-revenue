import { usePage } from "@inertiajs/inertia-vue3";
import { ref, watchEffect } from "vue";
import { useModal } from "@/Components/InertiaModal";

export const useFlash = () => {
    const page = usePage();
    const modal = useModal();
    const flash = ref(page.props?.value?.flash);

    //we compare json here to try and prevent double display of the alert. but it doesn't work.
    watchEffect(() => {
        const newFlash =
            modal.value?.page?.props?.flash || page.props?.value?.flash;
        if (JSON.stringify(flash.value) !== JSON.stringify(newFlash)) {
            // console.log({oldFlash: JSON.stringify(flash.value), newFlash: JSON.stringify(newFlash)})
            flash.value = newFlash;
        }
    });
    return flash;
};
