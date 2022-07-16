<template>
    <inertia-link
        :href="editUrl"
        class="flex flex-col items-center justify-center"
    >
        <user-avatar :opportunity="opportunity" />
        <user-info-summary
            :fullName="fullName"
            :agreementNumber="agreementNumber"
            :trialDates="trialDates"
            :trialMemberships="trialMemberships"
        />
    </inertia-link>
</template>
<script setup>
import UserAvatar from "./UserAvatar.vue";
import UserInfoSummary from "./UserInfoSummary.vue";
const props = defineProps({
    editUrl: {
        type: String,
    },
    opportunity: {
        type: String,
    },
    fullName: {
        type: String,
    },
    agreementNumber: {
        type: Number,
    },
    trialDates: {
        type: String,
    },
    trialMemberships: {
        type: Array,
    },
});
</script>
