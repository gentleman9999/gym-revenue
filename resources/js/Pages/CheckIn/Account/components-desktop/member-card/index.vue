<template>
    <check-in-card>
        <div class="flex flex-row justify-between p-6">
            <div class="flex flex-col">
                <img
                    :src="member.avatar"
                    width="150"
                    height="150"
                    class="member-avatar"
                />
                <h2 class="member-name">{{ member.name }}</h2>
                <div class="member-description">
                    Member Since {{ member.joinedAt }}
                </div>
                <div class="member-description">
                    Club ID# {{ member.clubId }}
                </div>
            </div>
            <div class="flex flex-col items-end space-y-6">
                <Button secondary size="xs">View History</Button>
                <application-logo class="app-logo" />
                <qr-icon />
            </div>
        </div>
        <membership-action />
    </check-in-card>
</template>
<style scoped>
.member-avatar {
    @apply rounded rounded-full border border-secondary;
}
.member-name {
    @apply text-3xl font-bold mt-4 mb-3;
}
.member-description {
    @apply text-lg;
}

.app-logo {
    @apply h-10 w-auto text-base-content;
}
</style>
<script setup>
import CheckInCard from "../../components/check-in-card.vue";
import MembershipAction from "./membership-action.vue";
import Button from "@/Components/Button.vue";
import ApplicationLogo from "@/Jetstream/ApplicationLogo.vue";
import QrIcon from "@/Components/Icons/Qr.vue";
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
