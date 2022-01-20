<template>
    <app-layout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">User Management</h2>
        </template>
        <gym-revenue-crud
            base-route="users"
            model-name="user"
            :fields="fields"
            :resource="users"
            :actions="actions"
        />
        <confirm
            title="Really Delete?"
            v-if="confirmDelete"
            @confirm="handleConfirmDelete"
            @cancel="confirmDelete = null"
        >
            Are you sure you want to delete user '{{ confirmDelete.name }}'? This action is permanent, and cannot be undone.
        </confirm>
    </app-layout>
</template>

<script>
import { defineComponent, watchEffect, ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";
import UserForm from "./Partials/UserForm";
import { Inertia } from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm";

export default defineComponent({
    components: {
        AppLayout,
        GymRevenueCrud,
        UserForm,
        Confirm
    },
    props: ["users", "filters"],
    setup() {
        const confirmDelete = ref(null);
        const handleClickDelete = (user) => {
            confirmDelete.value = user;
        };
        const handleConfirmDelete = () => {
            //TODO:
            // Inertia.delete(route("users.delete", confirmDelete.value.id));
            confirmDelete.value = null;
        };

        const fields = [
            "name",
            "created_at",
            "updated_at",
        ];

        const actions = {
            trash: false,
            restore: false,
            delete: {
                label: 'Delete',
                handler: ({data}) => handleClickDelete(data)
            }
        }
        return {
            confirmDelete,
            fields,
            actions,
            Inertia,
            handleConfirmDelete
        };
    },
});
</script>
