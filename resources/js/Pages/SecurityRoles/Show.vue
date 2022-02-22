<template>
    <app-layout :title="title">
        <!--
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">Security Roles</h2>
        </template>
        -->
        <page-toolbar-nav
            title="Security Roles"
            :links="navLinks"
        />
        <gym-revenue-crud
            base-route="security-roles"
            model-name="Security Role"
            model-key="role"
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
import PageToolbarNav from "@/Components/PageToolbarNav";

export default defineComponent({
    components: {
        AppLayout,
        GymRevenueCrud,
        Confirm,
        JetBarContainer,
        Button,
        PageToolbarNav
    },
    props: ["securityRoles", "filters"],
    setup(props) {
        console.log({securityRoles: props.securityRoles})

        const confirmTrash = ref(null);
        const handleClickTrash = (id) => {
            confirmTrash.value = id;
        };

        const handleConfirmTrash = () => {
            Inertia.delete(route("security-roles.trash", confirmTrash.value));
            confirmTrash.value = null;
        };

        const fields = ["security_role", "created_at", "updated_at"];

        let navLinks = [
            {
                label: "Users",
                href: route("users"),
                onClick: null,
                active: false
            },
            {
                label: "Security Roles",
                href: route("security-roles"),
                onClick: null,
                active: true
            }
        ];

        return {fields, confirmTrash, handleConfirmTrash, handleClickTrash, Inertia, navLinks};
    },
});
</script>
