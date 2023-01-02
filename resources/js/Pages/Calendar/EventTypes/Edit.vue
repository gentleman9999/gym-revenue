<template>
    <LayoutHeader title="Calendar Event Types">
        <jet-bar-icon type="g0back" fill />
        <h2 class="font-semibold text-xl leading-tight">Edit Event Type</h2>
    </LayoutHeader>

    <calendar-event-type-form
        :client-id="$page.props.user.client_id"
        :calendar-event-type="eventType"
    />
</template>

<script setup>
import { ref } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";

import JetBarIcon from "@/Components/JetBarIcon.vue";
import CalendarEventTypeForm from "@/Pages/Calendar/EventTypes/Partials/CalendarEventTypeForm.vue";

const props = defineProps({
    calendarEventType: {
        type: Object,
        required: true,
    },
});

const eventType = ref(props.calendarEventType);
</script>
