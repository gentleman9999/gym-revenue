<template>
    <LayoutHeader title="Edit Lead">
        <jet-bar-icon type="g0back" fill />
        <h2 class="font-semibold text-xl leading-tight">Edit Lead</h2>
    </LayoutHeader>

    <div class="badge badge-info" v-if="trialDates?.length">
        Trial Uses: {{ trialDates?.length || 0 }}
    </div>
    <div class="badge badge-error mt-4" v-if="lead.trial_memberships?.length">
        Trial Expires:
        {{ new Date(lead.trial_memberships[0].expiry_date).toLocaleString() }}
    </div>
    <lead-form
        :user-id="user_id"
        :lead="lead"
        :locations="locations"
        :lead_owners="lead_owners"
        :available_services="available_services"
        :interaction-count="interactionCount"
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
    entry_sources: {
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
    lead: {
        type: Object,
    },
    trialDates: {
        type: [Object, String],
    },
    interactionCount: {
        type: [String, Number],
    },
});
</script>
