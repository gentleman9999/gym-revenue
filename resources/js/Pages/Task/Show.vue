<template>
    <LayoutHeader title="Tasks" />
    <page-toolbar-nav title="Tasks" :links="navLinks" />
    <div class="container m-auto flex flex-row gap-4">
        <month-switcher class="pl-4" :onChange="switchMonth" />
        <button class="btn btn-secondary btn-sm" @click="handleClickNewTask">
            Create Task
        </button>
    </div>
    <div class="flex flex-row justify-center">
        <div class="flex flex-col items-center">
            <task-date-switcher
                :startOfTheWeek="startOfTheWeek"
                :setStartOfTheWeek="setStartOfTheWeek"
                :selectedDate="selectedDate"
                :setSelectedDate="setSelectedDate"
            />
            <task-list-view
                v-for="taskType in taskTypes"
                :updatePage="(value) => (param = { ...param, page: value })"
                :key="taskType"
                :task-type="taskType"
                :fields="fields"
                :on-double-click="handleDoubleClick"
                @edit="handleOnEdit"
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
            :locations="locations"
            :lead_users="lead_users"
            :member_users="member_users"
            :duration="{
                start: null,
            }"
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
            :locations="locations"
            @submitted="closeModals"
            ref="editCalendarEventForm"
            :duration="{
                start: selectedCalendarEvent.start,
                end: selectedCalendarEvent.end,
            }"
        />
    </daisy-modal>
</template>
<script>
import { defineComponent, ref, watch, computed } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import { Inertia } from "@inertiajs/inertia";
import Confirm from "@/Components/Confirm.vue";
import DaisyModal from "@/Components/DaisyModal.vue";
import Button from "@/Components/Button.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import CalendarEventForm from "@/Pages/Calendar/Partials/CalendarEventForm.vue";
import PageToolbarNav from "@/Components/PageToolbarNav.vue";
import TaskDateSwitcher from "./components/TaskDateSwitcher/TaskDataSwitcher.vue";
import MonthSwitcher from "./components/TaskDateSwitcher/MonthSwitcher.vue";
import TaskListView from "./components/TaskListView.vue";
import pickBy from "lodash/pickBy";
import { transformDate } from "@/utils/transformDate";

export default defineComponent({
    components: {
        LayoutHeader,
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
        "filters",
        "incomplete_tasks",
        "overdue_tasks",
        "completed_tasks",
        "lead_users",
        "member_users",
        "locations",
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
                label: "My Calendar",
                href: route("calendar.mine"),
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
            {
                label: "Reminders",
                href: route("reminders"),
                onClick: null,
                active: false,
            },
            {
                label: "QuickView",
                href: route("calendar.quickview"),
                onClick: null,
                active: false,
            },
        ];

        const taskTypes = [
            "incomplete_tasks",
            "overdue_tasks",
            "completed_tasks",
        ];

        const handleDoubleClick = ({ data }) => {
            openEventForm(data);
        };
        const openEventForm = (data) => {
            selectedCalendarEvent.value = data;
            editEventModal.value.open();
        };

        const handleOnEdit = (data) => {
            console.log("handeOnEdit", data);
            openEventForm(data);
        };
        const param = ref({
            page: 1,
        });

        return {
            fields,
            confirmDelete,
            handleConfirmDelete,
            handleClickDelete,
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
            selectedDate,
            setSelectedDate,
            startOfTheWeek,
            setStartOfTheWeek,
            taskTypes,
            selectedDateFormatted,
            switchMonth,
            handleDoubleClick,
            handleOnEdit,
            param,
        };
    },
});
</script>
