<template>
    <app-layout :title="title">

        <page-toolbar-nav
            title="Roles"
            :links="navLinks"
        />
        <gym-revenue-crud
            base-route="roles"
            model-name="Role"
            model-key="role"
            :fields="fields"
            :resource="roles"
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
                confirmTrash.role
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
    props: ["roles", "filters"],
    setup(props) {
        console.log({roles: props.roles})

        const confirmTrash = ref(null);
        const handleClickTrash = (id) => {
            confirmTrash.value = id;
        };

        const handleConfirmTrash = () => {
            Inertia.delete(route("roles.trash", confirmTrash.value));
            confirmTrash.value = null;
        };

        const fields = ["name", "created_at", "updated_at"];

        let navLinks = [
            {
                label: "Users",
                href: route("users"),
                onClick: null,
                active: false
            },
            {
                label: "Roles",
                href: route("roles"),
                onClick: null,
                active: true
            },
            {
                label: "Classification",
                href: route("classifications"),
                onClick: null,
                active: false
            }
        ];

        return {fields, confirmTrash, handleConfirmTrash, handleClickTrash, Inertia, navLinks};
    },
});
</script>
