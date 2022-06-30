<template>
    <ModalableWrapper>
        <LayoutHeader title="Calendar Event Types">
            <jet-bar-icon type="g0back" fill />
            <h2 class="font-semibold text-xl leading-tight">Edit Event Type</h2>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
            <calendar-event-type-form
                :client-id="$page.props.user.current_client_id"
                :calendar-event-type="calendarEventType"
            />
        </template>
    </ModalableWrapper>
</template>

<script>
import { defineComponent } from "vue";

import LayoutHeader from "@/Layouts/LayoutHeader";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import JetBarIcon from "@/Components/JetBarIcon";
import CalendarEventTypeForm from "@/Pages/Calendar/EventTypes/Partials/CalendarEventTypeForm";

import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";

export default defineComponent({
    components: {
        CalendarEventTypeForm,
        LayoutHeader,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        JetBarIcon,
        ModalableWrapper,
        ModalSlot,
    },
    props: {
        calendarEventType: {
            type: Object,
            required: true,
        },
    },
});
</script>
