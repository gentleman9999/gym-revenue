<template>
    <section>
        <location-container
            v-for="(events, ix) in calendar_events_by_locations"
            title="location x"
            date="April 6, 2022"
            :events="events"
            :ix="ix"
        />
    </section>
</template>

<style scoped>
section {
    @apply mx-24 grid grid-cols-3 gap-8;
}
</style>

<script setup>
import { ref } from "vue";

import LocationContainer from "./components/LocationContainer.vue";

const props = defineProps({
    calendar_events_by_locations: {
        type: Array,
        default: [],
    },
});

console.log("calendar eventsss", props.calendar_events_by_locations);
</script>
