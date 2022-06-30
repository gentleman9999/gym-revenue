<template>
    <LayoutHeader title="Edit Security Role">
        <jet-bar-icon type="g0back" fill />
        <h2 class="font-semibold text-xl leading-tight">Edit Security Role</h2>
    </LayoutHeader>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <security-role-form
                :client-id="$page.props.user.current_client_id"
                :availableAbilities="availableAbilities"
                :role="role"
                :security-groups="securityGroups"
            />
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetBarIcon from "@/Components/JetBarIcon.vue";
import SecurityRoleForm from "@/Pages/Roles/Partials/RoleForm.vue";

export default defineComponent({
    components: {
        SecurityRoleForm,
        LayoutHeader,
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
        securityGroups: {
            type: Array,
            default: [],
        },
    },
});
</script>
