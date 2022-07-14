<template>
    <LayoutHeader title="Edit Reminder">
        <jet-bar-icon type="goback" fill />
        <h2 class="font-semibold text-xl leading-tight">Edit Reminder</h2>
    </LayoutHeader>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <reminder-form
                :client-id="$page.props.user.current_client_id"
                :reminder="reminder"
            />
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetBarIcon from "@/Components/JetBarIcon.vue";

import ReminderForm from "@/Pages/Reminders/Partials/ReminderForm.vue";

export default defineComponent({
    components: {
        ReminderForm,
        LayoutHeader,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        JetBarIcon,
    },
    props: {
        reminder: {
            type: Object,
            required: true,
        },
    },
});
</script>
