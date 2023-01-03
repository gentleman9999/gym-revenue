<template>
    <LayoutHeader title="Edit Customer">
        <jet-bar-icon type="goback" fill />
        <h2 class="font-semibold text-xl leading-tight">Edit Customer</h2>
    </LayoutHeader>

    <customer-form
        :user-id="user_id"
        :client-id="this.$page.props.user.client_id"
        :customer="customer"
        :locations="locations"
        :available_services="available_services"
        :interaction-count="interactionCount"
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
    interactionCount: {
        type: [Number, String],
    },
    customer: {
        type: Object,
    },
});
</script>
