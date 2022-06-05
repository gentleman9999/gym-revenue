<template>
    <app-layout :title="title">
        <page-toolbar-nav title="Tasks" :links="navLinks" />
        <task-date-switcher
            :startOfTheWeek="startOfTheWeek"
            :setStartOfTheWeek="setStartOfTheWeek"
            :selectedDate="selectedDate"
            :setSelectedDate="setSelectedDate"
        />
        <gym-revenue-crud
            base-route="tasks"
            model-name="Task"
            model-key="task"
            :fields="fields"
            :resource="tasks"
            :actions="{
                edit: {
                    label: 'Edit',
                    handler: ({ data }) => editTask(data),
                },
                trash: false,
                restore: false,
                delete: {
                    label: 'Delete',
                    handler: ({ data }) => handleClickDelete(data),
                },
            }"
            :top-actions="topActions"
        />
        <confirm
            title="Really Trash Task?"
            v-if="confirmDelete"
            @confirm="handleConfirmDelete"
            @cancel="confirmDelete = null"
        >
            Are you sure you want to delete task'{{ confirmDelete.title }}'
        </confirm>

        <daisy-modal
            ref="createEventModal"
            id="createEventModal"
            @close="resetCreateEventModal"
        >
            <h1 class="font-bold mb-4">Create Event</h1>
            <calendar-event-form
                @submitted="closeModals"
                ref="createCalendarEventForm"
            />
        </daisy-modal>
        <daisy-modal
            ref="editEventModal"
            id="editEventModal"
            @close="resetEditEventModal"
            class="max-w-screen lg:max-w-[800px]"
        >
            <h1 class="font-bold mb-4">Edit Event</h1>
            <calendar-event-form
                v-if="selectedCalendarEvent"
                :calendar_event="selectedCalendarEvent"
                :key="selectedCalendarEvent"
                :client_users="client_users"
                :lead_users="lead_users"
                :client_id="client_id"
                @submitted="closeModals"
                ref="editCalendarEventForm"
            />
        </daisy-modal>
    </app-layout>
</template>
<script>
import { defineComponent, ref } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";
import { Inertia } from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm";
import DaisyModal from "@/Components/DaisyModal";
import Button from "@/Components/Button";
import JetBarContainer from "@/Components/JetBarContainer";
import CalendarEventForm from "@/Pages/Calendar/Partials/CalendarEventForm";
import PageToolbarNav from "@/Components/PageToolbarNav";
import TaskDateSwitcher from "./components/TaskDateSwitcher";

export default defineComponent({
    components: {
        AppLayout,
        GymRevenueCrud,
        Confirm,
        DaisyModal,
        JetBarContainer,
        Button,
        PageToolbarNav,
        CalendarEventForm,
        TaskDateSwitcher,
    },
    props: ["tasks", "filters"],
    setup(props) {
        const createEventModal = ref();
        const editEventModal = ref();
        const selectedCalendarEvent = ref(null);
        const createCalendarEventForm = ref(null);
        const editCalendarEventForm = ref(null);

        const resetCreateEventModal = () =>
            createCalendarEventForm.value?.form?.reset();
        const resetEditEventModal = () =>
            createCalendarEventForm.value?.form?.reset();
        const closeModals = () => {
            createEventModal.value.close();
            editEventModal.value.close();
        };

        const editTask = (item) => {
            console.log(item);
            const id = item.id;
            selectedCalendarEvent.value = props.tasks.data.find(
                (event) => event.id === id
            );
            editEventModal.value.open();
        };
        const handleClickNewTask = () => {
            selectedCalendarEvent.value = null;
            createEventModal.value.open();
        };

        const handleConfirmDelete = () => {
            Inertia.delete(route("tasks.delete", confirmDelete.value));
            confirmDelete.value = null;
        };
        const confirmDelete = ref(null);
        const handleClickDelete = (item) => {
            console.log("click delete", item);
            confirmDelete.value = item;
        };

        const selectedDate = ref(new Date());

        let startDay = new Date();
        let day = startDay.getDay() === 0 ? 7 : startDay.getDay();
        startDay.setDate(startDay.getDate() - day + 1);
        const startOfTheWeek = ref(startDay);
        const setSelectedDate = (val) => {
            selectedDate.value = val;
        };
        const setStartOfTheWeek = (val) => {
            selectedDate.value = val;
        };
        const fields = [
            "title",
            "created_at",
            "updated_at",
            {
                name: "event_completion",
                label: "Completed At",
            },
        ];

        let navLinks = [
            {
                label: "Calendar",
                href: route("calendar"),
                onClick: null,
                active: false,
            },
            {
                label: "Event Types",
                href: route("calendar.event_types"),
                onClick: null,
                active: false,
            },
            {
                label: "Tasks",
                href: route("tasks"),
                onClick: null,
                active: true,
            },
        ];

        const topActions = {
            create: {
                label: "New Task",
                handler: handleClickNewTask,
                class: "btn-primary",
            },
        };

        return {
            fields,
            confirmDelete,
            handleConfirmDelete,
            handleClickDelete,
            editTask,
            handleClickNewTask,
            Inertia,
            navLinks,
            createCalendarEventForm,
            editCalendarEventForm,
            resetCreateEventModal,
            resetEditEventModal,
            createEventModal,
            editEventModal,
            selectedCalendarEvent,
            closeModals,
            topActions,
            selectedDate,
            setSelectedDate,
            startOfTheWeek,
            setStartOfTheWeek,
        };
    },
});
</script>
