<template>
    <check-in-card class="items-center">
        <img
            :src="member.avatar"
            width="150"
            height="150"
            class="member-avatar"
        />
        <h2 class="member-name">{{ member.name }}</h2>
        <div class="member-description">Member Since {{ member.joinedAt }}</div>
        <div class="member-description">Club ID# {{ member.clubId }}</div>
        <membership-action />
    </check-in-card>
</template>
<style scoped>
.member-avatar {
    @apply rounded rounded-full border border-secondary mt-9;
}
.member-name {
    @apply text-3xl font-bold mt-4 mb-3;
}
.member-description {
    @apply text-lg;
}
</style>
<script setup>
import CheckInCard from "../../components/check-in-card.vue";
import MembershipAction from "./membership-action.vue";
const props = defineProps({
    member: {
        type: Object,
        default: {
            avatar: "/img/mocks/checkin-member.png",
            name: "Kevin Buchanan",
            joinedAt: "2020",
            clubId: "1234567890",
        },
    },
});
</script>
