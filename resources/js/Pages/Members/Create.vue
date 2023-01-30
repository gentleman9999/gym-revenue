<template>
    <LayoutHeader title="Create New Member">
        <h2 class="font-semibold text-xl leading-tight">Create New Member</h2>
    </LayoutHeader>

    <member-form
        :user-id="user_id"
        :client-id="$page.props.user.client_id"
        :locations="locations"
        :lead_types="lead_types"
        :lead_statuses="lead_statuses"
        :lead_sources="lead_sources"
        :lead_owners="lead_owners"
        :available_services="available_services"
    />
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import MemberForm from "@/Pages/Members/Partials/MemberForm.vue";

const props = defineProps({
    user_id: {
        type: String,
    },
    locations: {
        type: Array,
    },
    available_services: {
        type: Array,
    },
});
</script>
