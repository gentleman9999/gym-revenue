<template>
    <sweet-modal
        title="Rename File"
        width="85%"
        overlayTheme="dark"
        modal-theme="dark"
        enable-mobile-fullscreen
        ref="modal"
    >
       Hello World! {{user}} !!!!!
    </sweet-modal>

</template>

<script>
import SweetModal from "@/Components/SweetModal3/SweetModal";
import {ref} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";

export default {
name: "ImpersonationModal",
components: {SweetModal},
    setup(){
    const page = usePage();
    const modal = ref();
    function open(){
            console.log('hello world');
            modal.value.open();
        }
        const user = page.props.value.user.email;
        return {open, modal, user};
    }
}

</script>

<style scoped>

</style>
