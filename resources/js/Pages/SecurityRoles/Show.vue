<template>
    <app-layout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">Security Roles</h2>
        </template>
        <gym-revenue-crud
            base-route="security-roles"
            model-name="SecurityRole"
            :fields="fields"
            :resource="securityRoles"
            :actions="{
                trash: {
                    handler: ({ data }) => handleClickTrash(data),
                },
            }"
        />
        <confirm
            title="Really Trash Security Role?"
            v-if="confirmTrash"
            @confirm="handleConfirmTrash"
            @cancel="confirmTrash = null"
        >
            Are you sure you want to move Security Role '{{
                confirmTrash.security_role
            }}' to the trash?<BR />
        </confirm>
    </app-layout>
</template>
<script>
import { defineComponent, ref } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";
import { Inertia } from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm";

import Button from "@/Components/Button";
import JetBarContainer from "@/Components/JetBarContainer";

export default defineComponent({
    components: {
        AppLayout,
        GymRevenueCrud,
        Confirm,
        JetBarContainer,
        Button,
    },
    props: ["securityRoles", "filters"],
    setup(props) {
        console.log({securityRoles: props.securityRoles})

        const confirmTrash = ref(null);
        const handleClickTrash = (id) => {
            confirmTrash.value = id;
        };
        const handleConfirmTrash = () => {
            axios
                .delete(route("security-roles.trash", confirmTrash.value))
                .then(
                    (response) => {
                        setTimeout(() => response($result, 200), 10000);
                    },
                    Inertia.reload(),
                    //           location.reload(),
                    (confirmTrash.value = null)
                );
        };

        const fields = ["security_role"];

        return {fields, handleConfirmTrash, handleClickTrash, Inertia};
    },
});
</script>
