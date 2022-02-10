<template>
    <sweet-modal
        title="Impersonate"
        width="85%"
        overlayTheme="dark"
        modal-theme="dark"
        enable-mobile-fullscreen
        ref="modal"
    >
       Hello {{user}}

        <select v-model="impersonatedUserId">
            <option>Select A User To Impersonate</option>
            <option v-for="(impersonateableUser, i) in impersonateableUsers" :value="impersonateableUsers[i].id">{{impersonateableUsers[i].name}}</option>
        </select>

         <a :href="route('users.impersonate', this.impersonatedUserId)">Impersonate</a> <a :href="route('users.leave-impersonate')">Leave Impersonation</a>

    </sweet-modal>

</template>

<script>
import SweetModal from "@/Components/SweetModal3/SweetModal";
import {ref} from "vue";
import {usePage} from "@inertiajs/inertia-vue3";

export default {
name: "ImpersonationModal",
components: {SweetModal},
    props: ["users", "filters", "clubs", "teams"],
    data(){
        return{
            impersonatedUserId: 10
        }
    },
    setup(){
    const page = usePage();
    const modal = ref();
    function open(){
            console.log('hello world');
            modal.value.open();
            console.log('Users: ', page.props.value.users.data[0].name);
        }
        const user = page.props.value.user.name;
        const impersonateableUsers = page.props.value.users.data;
        return {open, modal, user, impersonateableUsers};
    }
}

</script>

<style scoped>

</style>
