<template>
    <section>
        <location-container
            v-for="event in calendar_events_by_locations"
            title="location x"
            date="April 6, 2022"
        />
    </section>
</template>

<style scoped>
section {
    @apply mx-12 grid grid-cols-3 gap-8;
}
</style>

<script setup>
import { ref } from "vue";

import LocationContainer from "./components/LocationContainer";

const props = defineProps({
    calendar_events_by_locations: {
        type: Array,
        default: [],
    },
});

console.log("calendar eventsss", props.calendar_events_by_locations);
</script>
