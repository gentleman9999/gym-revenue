<template>
    <LayoutHeader title="Create New Lead">
        <h2 class="font-semibold text-xl leading-tight">Create New Lead</h2>
    </LayoutHeader>

    <lead-form
        :user-id="user_id"
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
import LeadForm from "@/Pages/Leads/Partials/LeadForm.vue";

const props = defineProps({
    user_id: {
        type: String,
    },
    locations: {
        type: Array,
    },
    lead_types: {
        type: Array,
    },
    lead_sources: {
        type: Array,
    },
    available_services: {
        type: Array,
    },
    lead_owners: {
        type: Array,
    },
    lead_statuses: {
        type: Array,
    },
});
</script>
