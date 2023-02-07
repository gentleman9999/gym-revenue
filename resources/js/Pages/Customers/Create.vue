<template>
    <LayoutHeader title="Create New Customer">
        <h2 class="font-semibold text-xl leading-tight">Create New Customer</h2>
    </LayoutHeader>

    <customer-form
        :user-id="user_id"
        :client-id="$page.props.user.client_id"
        :locations="locations"
        :lead_types="lead_types"
        :lead_statuses="lead_statuses"
        :entry_sources="entry_sources"
        :lead_owners="lead_owners"
        :available_services="available_services"
        :agreement-categories="agreementCategories"
    />
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import CustomerForm from "@/Pages/Customers/Partials/CustomerForm.vue";

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
    agreementCategories: {
        type: Array,
    },
});
</script>
