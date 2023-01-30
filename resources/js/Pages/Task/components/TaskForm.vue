<template>
    <CalendarEventForm
        :calendar_event="event"
        :duration="{
            start: event?.start,
            end: event?.end,
        }"
        :locations="$page.props.locations"
    />
</template>
<script setup>
import CalendarEventForm from "@/Pages/Calendar/Partials/CalendarEventForm.vue";

const props = defineProps({
    event: Object,
});
</script>
