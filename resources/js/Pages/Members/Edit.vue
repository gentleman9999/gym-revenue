<template>
    <LayoutHeader title="Edit Member">
        <jet-bar-icon type="goback" fill />
        <h2 class="font-semibold text-xl leading-tight">Edit Member</h2>
    </LayoutHeader>

    <member-form
        :user-id="user_id"
        :client-id="this.$page.props.user.client_id"
        :member="member"
        :locations="locations"
        :available_services="available_services"
        :interaction-count="interactionCount"
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
    member: {
        type: Object,
    },
    available_services: {
        type: Array,
    },
    interactionCount: {
        type: [String, Number],
    },
});
</script>
