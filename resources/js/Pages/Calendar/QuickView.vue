<template>
    <section>
        <location-container
            v-for="events in calendar_events_by_locations"
            :events="events"
            :active_date="active_date"
            :visible_date="visible_date"
            @advance="() => upWeek()"
            @regress="() => downWeek()"
            @changeDate="(d) => updateDay(d)"
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

const getQueryDate = () => {
    const p = new Proxy(new URLSearchParams(window.location.search), {
        get: (searchParams, prop) => searchParams.get(prop),
    });

    if (!p.start) return new Date();
    return p.start;
};

const { form, reset, clearFilters, clearSearch } = useSearchFilter("calendar", {
    start: "",
});
/** Currently selected date - this is the date we're showing all events for */
const active_date = ref(new Date(getQueryDate()));
getQueryDate();

/** Show events from the selected date */
const updateDay = (d) => {
    let q_date = d instanceof Date ? d : new Date(d);
    active_date.value = q_date;

    const q = new URLSearchParams();

    let d_year = q_date.getFullYear();
    let d_month = q_date.getMonth() + 1;
    let d_day = q_date.getDate();

    let qdStr = `${d_year}-${d_month}-${d_day}`;
    q.set("start", qdStr);

    window.location.search = q;
};

/**
 * some date within the currently visible week we want to show.
 * the entierty of our week's dates are derived from this value
 */
const visible_date = ref(new Date(getQueryDate()));

const upWeek = () => {
    let td = new Date(visible_date.value);
    let temp = new Date(td.getFullYear(), td.getMonth(), td.getDate() + 7);

    visible_date.value = temp;
};

const downWeek = () => {
    let td = new Date(visible_date.value);
    let temp = new Date(td.getFullYear(), td.getMonth(), td.getDate() - 7);

    visible_date.value = temp;
};

const start = ref(null);

console.log("calendar events (quick-view)", props.calendar_events_by_locations);
</script>
