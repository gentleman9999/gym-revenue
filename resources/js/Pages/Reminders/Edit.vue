<template>
    <LayoutHeader title="Edit Security Role">
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
import LayoutHeader from "@/Layouts/LayoutHeader";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import JetBarIcon from "@/Components/JetBarIcon";
import ReminderForm from "@/Pages/Reminders/Partials/ReminderForm";

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
