<template>
    <LayoutHeader title="Team Settings">
        <h2 class="font-semibold text-xl leading-tight">Team Settings</h2>
    </LayoutHeader>

    <team-form :team="team" />

    <team-member-manager
        class="mt-10 sm:mt-0"
        :team="team"
        :available-roles="availableRoles"
        :available-users="availableUsers"
        :user-permissions="permissions"
        :users="users"
        :availableUsers="availableUsers"
    />
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import TeamMemberManager from "@/Pages/Teams/Partials/TeamMemberManager.vue";
import TeamForm from "./Partials/TeamForm.vue";

const props = defineProps({
    team: {
        type: Object,
    },
    availableRoles: {
        type: [Array, Object],
    },
    permissions: {
        type: [Array, Object],
    },
    users: {
        type: [Array, Object],
    },
    availableUsers: {
        type: [Array, Object],
    },
    availableLocations: {
        type: [Array, Object],
    },
});
</script>
