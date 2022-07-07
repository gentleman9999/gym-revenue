<template>
    <LayoutHeader title="Create Security Role">
        <h2 class="font-semibold text-xl leading-tight">Create Role</h2>
    </LayoutHeader>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <security-role-form
                :client-id="$page.props.user.current_client_id"
                :available-roles="availableRoles"
                :available-abilities="availableAbilities"
                :security-groups="securityGroups"
            />
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";

import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";

import ReminderForm from "@/Pages/Reminders/Partials/ReminderForm.vue";

export default defineComponent({
    components: {
        ReminderForm,
        LayoutHeader,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
    },
    props: {
        availableRoles: {
            type: Array,
            default: [],
        },
        availableAbilities: {
            type: Array,
            default: [],
        },
        securityGroups: {
            type: Array,
            default: [],
        },
    },
});
</script>
