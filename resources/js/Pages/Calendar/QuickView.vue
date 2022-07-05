<template>
    <section>
        <location-container
            v-for="events in calendar_events_by_locations"
            :events="events"
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
import { useSearchFilter } from "@/Components/CRUD/helpers/useSearchFilter";

const props = defineProps({
    calendar_events_by_locations: {
        type: Array,
        default: [],
    },
});

const { form, reset, clearFilters, clearSearch } = useSearchFilter("calendar", {
    start: "",
});

const start = ref(null);

console.log("calendar eventsss", props.calendar_events_by_locations);
</script>
