<template>
    <app-layout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">Calendar</h2>
        </template>
        <page-toolbar-nav
            title="Event Types"
            :links="navLinks"
        />

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="flex flex-row col-span-3 lg:col-span-2 gap-2 mb-4">
                <button class="btn btn-sm text-xs" @click="handleClickNewEvent">
                    New Event
                </button>
                <div class="flex-grow" />
                <simple-search-filter
                    v-model:modelValue="form.search"
                    class="w-full max-w-md mr-4 col-span-3 lg:col-span-1"
                    @reset="reset"
                    @clear-filters="clearFilters"
                    @clear-search="clearSearch"
                >
                    <div
                        class="block py-2 text-xs text-base-content text-opacity-80"
                    >
                        Type:
                    </div>
                    <select
                        v-model="form.calendar_event_type"
                        class="mt-1 w-full form-select"
                    >
                        <option :value="null" />
                        <option
                            v-for="{ name, id } in calendar_event_types"
                            :value="id"
                        >
                            {{ name }}
                        </option>
                    </select>
                    <div
                        class="block py-2 text-xs text-base-content text-opacity-80"
                    >
                        Trashed:
                    </div>
                    <select
                        v-model="form.trashed"
                        class="mt-1 w-full form-select"
                    >
                        <option :value="null" />
                        <option value="with">With Trashed</option>
                        <option value="only">Only Trashed</option>
                    </select>
                </simple-search-filter>
            </div>

            <FullCalendar :options="calendarOptions" ref="calendar" />
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
                    @submitted="closeModals"
                    ref="editCalendarEventForm"
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
import SimpleSearchFilter from "@/Components/CRUD/SimpleSearchFilter";
import { useSearchFilter } from "@/Components/CRUD/helpers/useSearchFilter";
import PageToolbarNav from "@/Components/PageToolbarNav";

export default defineComponent({
    components: {
        SimpleSearchFilter,
        CalendarEventForm,
        DaisyModal,
        AppLayout,
        GymRevenueCrud,
        SweetModal,
        FullCalendar,
        PageToolbarNav
    },
    props: [
        "sessions",
        "calendar_events",
        "calendar_event_types",
        "title",
        "isClientUser",
        "filters",
        "client_users",
    ],

    setup(props) {
        const { form, reset, clearFilters, clearSearch } = useSearchFilter(
            "calendar",
            { start: "", end: "" }
        );
        const calendar = ref(null);
        const createEventModal = ref();
        const editEventModal = ref();
        const selectedCalendarEvent = ref(null);
        const createCalendarEventForm = ref(null);
        const editCalendarEventForm = ref(null);
        const handleClickNewEvent = () => {
            selectedCalendarEvent.value = null;
            createEventModal.value.open();
        };

        const handleDroppedEvent = (data) => {
            let calendarEvent = {
                id: data.event.id,
                title: data.event.title,
                description: data.event.extendedProps.description,
                full_day_event: data.event.extendedProps.full_day_event,
                start: data.event.startStr.slice(0, 19).replace("T", " "),
                end: data.event.endStr.slice(0, 19).replace("T", " "),
                event_type_id: data.event.extendedProps.event_type_id,
                client_id: data.event.extendedProps.client_id,
            };
            Inertia.put(
                route("calendar.event.update", calendarEvent.id),
                calendarEvent
            );
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

        const updateStartEnd = (start, end) => {
            form.value.start = start;
            form.value.end = end;
        };

        const numClicks = ref(null);
        const resetCreateEventModal = () =>
            createCalendarEventForm.value?.form?.reset();
        const resetEditEventModal = () =>
            createCalendarEventForm.value?.form?.reset();

        let navLinks = [
            {
                label: "Calendar",
                href: route("calendar"),
                onClick: null,
                active: true
            },
            {
                label: "Event Types",
                href: route("calendar.event_types"),
                onClick: null,
                active: false
            },
        ];

        return {
            Inertia,
            calendarOptions: {
                plugins: [
                    dayGridPlugin,
                    timeGridPlugin,
                    interactionPlugin,
                    listPlugin,
                ],
                /*
                eventContent: function(arg) {
                    console.error(arg)
                    if (arg.event.extendedProps.type.type === "External Event") {
                       console.log('yes')
                    } else {
                       console.log('Event Type '+arg.event.extendedProps.type.type )
                    }
                },*/
                initialView: "dayGridMonth",
                events: (
                    { start, end, startStr, endStr },
                    successCallback,
                    failureCallback
                ) => {
                    updateStartEnd(startStr, endStr);
                    successCallback(props.calendar_events);
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
                    if (
                        ["timeGridDay", "timeGridWeek"].includes(
                            data?.view?.type
                        )
                    ) {
                        createCalendarEventForm.value.form.start = data.start;
                        createCalendarEventForm.value.form.end = data.end;
                        createEventModal.value.open();
                    }
                },
                dateClick: function (data) {
                    console.log({
                        data,
                        createCalendarEventForm: createCalendarEventForm.value,
                    });
                    numClicks.value++;
                    let singleClickTimer;
                    if (numClicks.value === 1) {
                        singleClickTimer = setTimeout(() => {
                            numClicks.value = 0;
                        }, 400);
                    } else if (numClicks.value === 2) {
                        clearTimeout(singleClickTimer);
                        numClicks.value = 0;

                        createCalendarEventForm.value.form.start = data.date;
                        createEventModal.value.open();
                    }
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
                eventDrop: function (data) {
                    handleDroppedEvent(data);
                },
            },
            createEventModal,
            editEventModal,
            handleClickNewEvent,
            selectedCalendarEvent,
            clearSelectedEvent,
            calendar,
            closeModals,
            form,
            reset,
            clearSearch,
            clearFilters,
            createCalendarEventForm,
            editCalendarEventForm,
            resetCreateEventModal,
            resetEditEventModal,
            navLinks
        };
    },
});
</script>
