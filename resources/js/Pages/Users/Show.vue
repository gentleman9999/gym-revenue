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
            :actions="{restore: false}"
        />
    </app-layout>
</template>

<script>
import { defineComponent, watchEffect, ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";
import UserForm from "./Partials/UserForm";
import { Inertia } from "@inertiajs/inertia";

export default defineComponent({
    components: {
        AppLayout,
        GymRevenueCrud,
        UserForm,
    },
    props: ["users", "filters"],
    setup() {
        const selectedFile = ref(null);
        const modal = ref(null);

        const fields = [
            "name",
            "created_at",
            "updated_at",
        ];
        return {
            modal,
            selectedFile,
            fields,
            Inertia,
        };
    },
});
</script>
