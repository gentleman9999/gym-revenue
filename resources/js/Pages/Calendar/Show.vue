<template>
    <app-layout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">Calendar</h2>
        </template>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="flex flex-row col-span-3 lg:col-span-2 gap-2">
                <button class="btn btn-sm text-xs" @click="handleClickNewEvent">
                    New Event
                </button>
            </div>

            <FullCalendar :options="calendarOptions" ref="calendar" />
            <daisy-modal ref="createEventModal" id="createEventModal">
                <h1 class="font-bold mb-4">Create Event</h1>
                <calendar-event-form @submitted="closeModals" />
            </daisy-modal>
            <daisy-modal ref="editEventModal" id="editEventModal">
                <h1 class="font-bold mb-4">Edit Event</h1>
                <calendar-event-form
                    v-if="selectedCalendarEvent"
                    :calendar_event="selectedCalendarEvent"
                    :key="selectedCalendarEvent"
                    @submitted="closeModals"
                />
            </daisy-modal>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent, watch, watchEffect, ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";
import SweetModal from "@/Components/SweetModal3/SweetModal";
import { Inertia } from "@inertiajs/inertia";
import "@fullcalendar/core/vdom"; // solves problem with Vite
import FullCalendar, {
    CalendarOptions,
    EventApi,
    DateSelectArg,
    EventClickArg,
} from "@fullcalendar/vue3";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import interactionPlugin from "@fullcalendar/interaction";
import listPlugin from "@fullcalendar/list";
import DaisyModal from "@/Components/DaisyModal";
import CalendarEventForm from "@/Pages/Calendar/Partials/CalendarEventForm";

export default defineComponent({
    components: {
        CalendarEventForm,
        DaisyModal,
        AppLayout,
        GymRevenueCrud,
        SweetModal,
        FullCalendar,
    },
    props: ["sessions", "calendar_events", "title", "isClientUser", "filters"],

    setup(props) {
        const calendar = ref(null);
        const createEventModal = ref();
        const editEventModal = ref();
        const selectedCalendarEvent = ref(null);
        const handleClickNewEvent = () => {
            selectedCalendarEvent.value = null;
            createEventModal.value.open();
        };
        const clearSelectedEvent = () => (selectedCalendarEvent.value = null);
        watchEffect(() => {
            console.log("events changed!");
            if (!props.calendar_events) {
                return;
            }
            const fullCalendarApi = calendar.value?.getApi();
            if (fullCalendarApi) {
                fullCalendarApi.refetchEvents();
                console.log("refetched events", props.calendar_events);
            }
        });

        const closeModals = () => {
            createEventModal.value.close();
            editEventModal.value.close();
        };
        return {
            Inertia,
            calendarOptions: {
                plugins: [
                    dayGridPlugin,
                    timeGridPlugin,
                    interactionPlugin,
                    listPlugin,
                ],
                initialView: "dayGridMonth",
                events: (info, successCallback, failureCallback) => {
                    //set window.location
                    console.log({info});
                    successCallback(props.calendar_events)
                },
                headerToolbar: {
                    left: "timeGridDay,timeGridWeek,dayGridMonth,listWeek",
                    center: "title",
                    right: "prev,next today",
                },
                editable: true,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                weekends: true,
                select: function (data) {
                    console.log("select. " + data);
                },
                eventClick: function (data) {
                    data.jsEvent.preventDefault(); // don't let the browser navigate
                    const id = data.event.id;
                    if (!id) {
                        return;
                    }
                    // const response = await axios.get(route('calendar.event.show', id));
                    // console.log("event clicked: ", response.data);
                    selectedCalendarEvent.value = props.calendar_events.find(
                        (event) => event.id === id
                    );
                    editEventModal.value.open();
                    console.log("event clicked: ", selectedCalendarEvent.value);
                },
            },
            createEventModal,
            editEventModal,
            handleClickNewEvent,
            selectedCalendarEvent,
            clearSelectedEvent,
            calendar,
            closeModals,
        };
    },
});
</script>
