<template>
    <app-layout title="Calendar Event Types">
        <template #header>
            <jet-bar-icon type="g0back" fill />
            <h2 class="font-semibold text-xl leading-tight">
                Edit Event Type
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <calendar-event-type-form
                    :client-id="$page.props.user.current_client_id"
                    :calendar-event-type="calendarEventType"
                />
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";

import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import JetBarIcon from "@/Components/JetBarIcon";
import CalendarEventTypeForm from "@/Pages/Calendar/EventTypes/Partials/CalendarEventTypeForm";

export default defineComponent({
    components: {
        CalendarEventTypeForm,
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        JetBarIcon,
    },
    props: {
        calendarEventType: {
            type: Object,
            required: true,
        }
    },
});
</script>
