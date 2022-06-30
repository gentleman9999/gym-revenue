<template>
    <LayoutHeader title="Reminders Management">
        <h2 class="font-semibold text-xl leading-tight">Reminders Manager</h2>
    </LayoutHeader>
    <gym-revenue-crud
        base-route="reminders"
        model-name="Reminder"
        model-key="reminder"
        :fields="fields"
        :resource="reminders"
        titleField="remindername"
        :card-component="ReminderDataCard"
        :actions="{
            edit: false,
            rename: {
                label: 'Rename',
                handler: ({ data }) => {
                    selectedReminder = data;
                },
            },
            permissions: {
                label: 'Permissions',
                handler: ({ data }) => {
                    selectedReminderPermissions = data;
                },
            },
        }"
        :top-actions="{
            create: {
                label: 'Create',
                handler: () => {
                    Inertia.visitInModal(route('reminders.create'));
                },
            },
        }"
    />
    <daisy-modal
        id="filenameModal"
        ref="filenameModal"
        @close="selectedFile = null"
    >
        <file-form
            :file="selectedFile"
            v-if="selectedFile"
            @success="filenameModal.close"
        />
    </daisy-modal>

    <daisy-modal
        ref="permissionsModal"
        id="permissionsModal"
        @close="selectedFilePermissions = null"
    >
        <h1 class="font-bold mb-4">Modify File Permissions</h1>
        <Permissions-Form
            :file="selectedFilePermissions"
            v-if="selectedFilePermissions"
            @success="permissionsModal.close"
        />
    </daisy-modal>
</template>

<style scoped>
td > div {
    @apply h-16;
}
</style>

<script>
import { defineComponent, watchEffect, ref } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";
import ReminderForm from "./Partials/ReminderForm";
import PermissionsForm from "./Partials/PermissionsForm";
import ReminderDataCard from "./Partials/ReminderDataCard";
import { Inertia } from "@inertiajs/inertia";
import DaisyModal from "@/Components/DaisyModal";
import RemindernameField from "@/Pages/Reminders/Partials/RemindernameField";

export default defineComponent({
    components: {
        LayoutHeader,
        GymRevenueCrud,
        ReminderForm,
        DaisyModal,
        PermissionsForm,
    },
    props: ["reminders"],
    setup() {
        const fields = [
            { name: "Name", component: RemindernameField },
            "Description",
            "Reminder Time",
            "Triggered",
        ];
        return {
            fields,
            ReminderDataCard,
            Inertia,
        };
    },
});
</script>
