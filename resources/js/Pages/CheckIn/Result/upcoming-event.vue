<template>
    <card-with-title title="Upcoming Event" accent>
        <div class="text-center">
            <div class="upcoming-event-title">{{ eventName }}</div>
            <Button secondary @click="$emit('showModal', 'event')"
                >Check in Now</Button
            >
            <div class="upcoming-event-to-detail">See Calendar Event</div>
        </div>
    </card-with-title>
</template>
<style scoped>
.upcoming-event-title {
    @apply text-3xl text-base-content mt-4 mb-3;
}
.upcoming-event-to-detail {
    @apply text-base-content mb-6 mt-3;
}
</style>
<script setup>
import CardWithTitle from "../components/card-with-title.vue";
import Button from "@/Components/Button.vue";

const props = defineProps({
    eventName: String,
});
</script>
