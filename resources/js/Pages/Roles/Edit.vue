<template>
    <app-layout title="Edit Security Role">
        <template #header>
            <jet-bar-icon type="g0back" fill />
            <h2 class="font-semibold text-xl leading-tight">
                Edit Security Role
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <security-role-form
                    :client-id="$page.props.user.current_client_id"
                    :availableAbilities="availableAbilities"
                    :role="$page.props.role"
                />
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";

import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import JetBarIcon from "@/Components/JetBarIcon";
import SecurityRoleForm from "@/Pages/Roles/Partials/RoleForm";

export default defineComponent({
    components: {
        SecurityRoleForm,
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        JetBarIcon,
    },
    props: {
        role: {
            type: Object,
            required: true,
        },
        availableRoles: {
            type: Array,
            default: [],
        },
        availableAbilities: {
            type: Array,
            default: [],
        },
    },
});
</script>
