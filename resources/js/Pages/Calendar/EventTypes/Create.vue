<template>
    <LayoutHeader title="Calendar Event Types">
        <h2 class="font-semibold text-xl leading-tight">Create Event Type</h2>
    </LayoutHeader>

    <calendar-event-type-form :client-id="$page.props.user.client_id" />
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import CalendarEventTypeForm from "@/Pages/Calendar/EventTypes/Partials/CalendarEventTypeForm.vue";
</script>
