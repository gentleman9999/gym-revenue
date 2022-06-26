<template>
    <app-layout :title="title">
        <page-toolbar-nav title="Tasks" :links="navLinks" />
        <div class="flex flex-row justify-center">
            <!--            hide month switcher until it does something-->
            <month-switcher class="pl-4" :onChange="switchMonth" />
            <div class="flex flex-col items-center">
                <task-date-switcher
                    :startOfTheWeek="startOfTheWeek"
                    :setStartOfTheWeek="setStartOfTheWeek"
                    :selectedDate="selectedDate"
                    :setSelectedDate="setSelectedDate"
                />
                <task-list-view
                    v-for="taskType in taskTypes"
                    :key="taskType"
                    :taskType="taskType"
                    base-route="tasks"
                    model-name="Task"
                    model-key="task"
                    :fields="fields"
                    :resource="getTaskData(taskType)"
                    :actions="{
                        edit: {
                            label: 'Edit',
                            handler: ({ data }) => editTask(data, taskType),
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
            </div>
        </div>
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
                :member_users="member_users"
                :client_id="client_id"
                @submitted="closeModals"
                ref="editCalendarEventForm"
            />
        </daisy-modal>
    </app-layout>
</template>
<script>
import { defineComponent, ref, watch, computed } from "vue";
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
import MonthSwitcher from "./components/TaskDateSwitcher/MonthSwitcher";
import TaskListView from "./components/TaskListView";
import pickBy from "lodash/pickBy";
import { transformDate } from "@/utils/transformDate";

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
        MonthSwitcher,
        TaskListView,
    },
    props: [
        "tasks",
        "filters",
        "incomplete_tasks",
        "overdue_tasks",
        "completed_tasks",
        "lead_users",
        "member_users",
    ],
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
            createEventModal.value?.close();
            editEventModal.value?.close();
        };

        const editTask = (item, taskType) => {
            console.log(props.tasks);
            const id = item.id;
            const taskData = getTaskData(taskType);
            selectedCalendarEvent.value = taskData.data.find(
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

        const selectedDateFormatted = computed(() =>
            transformDate(selectedDate.value)
        );

        let startDay = new Date();
        let day = startDay.getDay() === 0 ? 7 : startDay.getDay();
        startDay.setDate(startDay.getDate() - day + 1);
        const startOfTheWeek = ref(startDay);
        const setSelectedDate = (val) => {
            selectedDate.value = val;
        };
        const setStartOfTheWeek = (val) => {
            startOfTheWeek.value = val;
        };
        const switchMonth = (month) => {
            let start_date = new Date(
                startOfTheWeek.value.getFullYear(),
                month,
                startOfTheWeek.value.getDate()
            );
            setStartOfTheWeek(start_date);
        };
        const fields = [
            {
                name: "title",
                label: "Title",
            },
            {
                name: "start",
                label: "Due At",
            },
            {
                name: "created_at",
                label: "Created At",
            },
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

        const taskTypes = [
            "incomplete_tasks",
            "ovedue_tasks",
            "completed_tasks",
        ];
        const getData = function () {
            let options = {
                preserveState: true,
                preserveScroll: true,
            };
            let query = {
                start: selectedDateFormatted.value,
            };
            Inertia.get(route("tasks"), pickBy(query), options);
        };
        watch([selectedDate], getData, { deep: true });

        const getTaskData = (taskType) => {
            switch (taskType) {
                case "incomplete_tasks":
                    return props.incomplete_tasks;
                case "ovedue_tasks":
                    return props.overdue_tasks;
                case "completed_tasks":
                    return props.completed_tasks;
            }
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
            taskTypes,
            selectedDateFormatted,
            getTaskData,
            switchMonth,
        };
    },
});
</script>
