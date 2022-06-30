<template>
    <h1>test</h1>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
    calendar_events_by_locations: {
        type: Array,
        default: [],
    },
});

console.log("calendar eventsss", props.calendar_events_by_locations);
</script>
