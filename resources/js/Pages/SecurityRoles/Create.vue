<template>
    <app-layout title="Create Security Role">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">
                Create Security Role
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <security-role-form
                    :client-id="$page.props.user.current_client_id"
                    :availableRoles="availableRoles"
                    :availableAbilities="availableAbilities"
                />
            </div>
        </div>
    </app-layout>
</template>

<script>
import {defineComponent} from "vue";
import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";

import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";

import SecurityRoleForm from "@/Pages/SecurityRoles/Partials/SecurityRoleForm";

export default defineComponent({
    components: {
        SecurityRoleForm,
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
    },
    props: {
        availableRoles: {
            type: Array,
            default: []
        },
        availableAbilities: {
            type: Array,
            default: []
        }
    }
});
</script>
