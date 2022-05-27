import { usePage } from "@inertiajs/inertia-vue3";
import { computed, ref, watchEffect } from "vue";
import { useModal } from "@/Components/InertiaModal";

export const useFlash = () => {
    const page = usePage();
    const modal = useModal();
    const flash = ref(page.props?.value?.flash);

    const modalFlash = computed(() => modal.value?.page.props);
    console.log({ flash, modal, modalFlash: modalFlash.value });

    //we compare json here to prevent double display of the alert

    watchEffect(() => {
        console.log({ modalprops: modal.value?.page?.props?.flash });
        const newFlash =
            modal.value?.page?.props?.flash || page.props?.value?.flash;
        if (JSON.stringify(flash.value) !== JSON.stringify(newFlash)) {
            // console.log({oldFlash: JSON.stringify(flash.value), newFlash: JSON.stringify(newFlash)})
            flash.value = newFlash;
        }
    });
    return flash;
};
