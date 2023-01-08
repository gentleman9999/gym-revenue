<template>
    <LayoutHeader title="Edit Reminder">
        <jet-bar-icon type="goback" fill />
        <h2 class="font-semibold text-xl leading-tight">Edit Reminder</h2>
    </LayoutHeader>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <reminder-form
                :client-id="$page.props.user.client_id"
                :reminder="reminder"
            />
        </div>
    </div>
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import ReminderForm from "@/Pages/Reminders/Partials/ReminderForm.vue";

const props = defineProps({
    reminder: {
        type: Object,
        required: true,
    },
});
</script>
