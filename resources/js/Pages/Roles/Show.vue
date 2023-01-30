<template>
    <LayoutHeader title="Security Roles" />
    <page-toolbar-nav title="Security Roles" :links="navLinks" />
    <gym-revenue-crud
        base-route="roles"
        model-name="Role"
        model-key="role"
        :fields="fields"
        :edit-component="RoleForm"
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
</template>
<script>
import { defineComponent, ref, computed } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud.vue";
import { Inertia } from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm.vue";

import Button from "@/Components/Button.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import PageToolbarNav from "@/Components/PageToolbarNav.vue";
import RoleForm from "@/Pages/Roles/Partials/RoleForm.vue";
import { useMutation } from "@vue/apollo-composable";
import mutations from "@/gql/mutations";

export default defineComponent({
    components: {
        LayoutHeader,
        GymRevenueCrud,
        Confirm,
        JetBarContainer,
        Button,
        PageToolbarNav,
        RoleForm,
    },
    props: ["filters"],
    setup(props, context) {
        const confirmDelete = ref(null);
        const handleClickDelete = (item) => {
            confirmDelete.value = item;
        };

        const { mutate: deleteRole } = useMutation(mutations.role.delete);
        const handleConfirmDelete = async () => {
            await deleteRole({
                id: confirmDelete.value.id,
            });
            confirmDelete.value = null;
        };

        const fields = ["title", "created_at", "updated_at"];

        let navLinks = [
            {
                label: "Users",
                href: route("users"),
                onClick: null,
                active: false,
            },
            {
                label: "Security Roles",
                href: route("roles"),
                onClick: null,
                active: true,
            },
            {
                label: "Departments",
                href: route("departments"),
                onClick: null,
                active: false,
            },
            {
                label: "Positions",
                href: route("positions"),
                onClick: null,
                active: false,
            },
        ];

        return {
            fields,
            confirmDelete,
            handleConfirmDelete,
            handleClickDelete,
            Inertia,
            navLinks,
            RoleForm,
        };
    },
});
</script>
