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
                trash: false,
                restore: false,
                delete: {
                    label: 'Delete',
                    handler: ({ data }) => handleClickDelete(data),
                },
            }"
        />
        <confirm
            title="Really Trash Security Role?"
            v-if="confirmDelete"
            @confirm="handleConfirmDelete"
            @cancel="confirmDelete = null"
        >
            Are you sure you want to delete Security Role '{{
                confirmDelete.title
            }}'
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

        const confirmDelete = ref(null);
        const handleClickDelete = (item) => {
            console.log('click delete', item);
            confirmDelete.value = item;
        };

        const handleConfirmDelete = () => {
            Inertia.delete(route("roles.delete", confirmDelete.value));
            confirmDelete.value = null;
        };

        const fields = ["title", "created_at", "updated_at"];

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

        return {fields, confirmDelete, handleConfirmDelete, handleClickDelete, Inertia, navLinks};
    },
});
</script>
