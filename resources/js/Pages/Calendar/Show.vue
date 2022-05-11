<template>
    <app-layout :title="title">
<!--        <template #header>-->
<!--            <h2 class="font-semibold text-xl leading-tight">Calendar</h2>-->
<!--        </template>-->
        <page-toolbar-nav
            title="Event Types"
            :links="navLinks"
        />

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="flex flex-row items-center gap-4 mb-4">
                <h1 class="text text-xl mr-8">Calendar</h1>
                <h2 class="text text-xl font-bold">{{title}}</h2>
                <div class="flex-grow" />
<!--                <button class="btn btn-sm text-xs btn-primary" @click="handleClickNewEvent">-->
<!--                    New Event-->
<!--                </button>-->
                <div class="form-control">
                    <select v-model="currentView" @change="handleChangeView">
                        <option value="dayGridMonth">Month</option>
                        <option value="timeGridWeek">Week</option>
                        <option value="timeGridDay">Day</option>
                    </select>
                </div>
                <simple-search-filter
                    v-model:modelValue="form.search"
                    class="w-full max-w-md mr-4 col-span-3 lg:col-span-1"
                    @reset="reset"
                    @clear-filters="clearFilters"
                    @clear-search="clearSearch"
                >
                    <div class="block py-2 text-xs text-base-content text-opacity-80">
                        View User Calendar:
                    </div>
                    <select
                        v-model="form.viewUser"
                        class="mt-1 w-full form-select"
                    >
                        <option :value="null" />
                        <option
                            v-for="{ name, id } in client_users"
                            :value="id"
                        >
                            {{ name }}
                        </option>
                    </select>

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
                    :lead_users="lead_users"
                    :client_id="client_id"
                    @submitted="closeModals"
                    ref="editCalendarEventForm"
                />
            </daisy-modal>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent, watch, watchEffect, ref, computed } from "vue";
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
        "lead_users",
        "client_id"
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
        const currentView = ref('timeGridWeek');
        const start = ref(null);
        const end = ref(null);

        const title = computed(()=>start.value?.toLocaleString('default', { month: 'long', year: 'numeric' }));

        const handleClickNewEvent = () => {
            selectedCalendarEvent.value = null;
            createEventModal.value.open();
        };

        const handleChangeView = () =>{
            console.log('handleChangeView Called');
            calendar.value.getApi().changeView(currentView.value);
            onViewChanged();
        }

        const onViewChanged = () => {
            start.value = calendar.value.getApi().view.activeStart;
            start.end = calendar.value.getApi().view.activeEnd;
            console.log({start,end,calendarView: calendar.value.getApi().view})
        }

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
            if (!props.calendar_events) {
                return;
            }
            const fullCalendarApi = calendar.value?.getApi();
            if (fullCalendarApi) {
                fullCalendarApi.refetchEvents();
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

        const dowIntToString = (dow) => {
            switch(dow){
                case 0:
                    return 'Sunday';
               case 1:
                   return 'Monday';
               case 2:
                   return "Tuesday";
                case 3:
                    return "Wednesday";
                case 4:
                    return "Thursday";
                case 5:
                    return "Friday";
                case 6:
                    return "Saturday";
            }
        }

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
                initialView: "timeGridWeek",
                events: (
                    { start, end, startStr, endStr },
                    successCallback,
                    failureCallback
                ) => {
                    updateStartEnd(startStr, endStr);
                    successCallback(props.calendar_events);
                },
                headerToolbar: {
                    left: "",
                    center: "",
                    right: "",
                    // center: "title",
                    // right: "prev,next today",
                },
                views: {
                    timeGridWeek: {
                        eventTimeFormat: {
                            hour: 'numeric',
                            minute: '2-digit',
                            meridiem: 'short'
                        }
                    },
                    dayGridMonth: {
                        dayHeaderFormat: {
                            weekday: 'long'
                        }
                    },
                    timeGridDay:{
                        dayHeaderFormat: {
                            weekday: 'long',
                            day: 'numeric'
                        },
                        nowIndicator: true
                    }
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
                viewDidMount: onViewChanged,
                dateClick: function (data) {
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
                },
                eventDrop: function (data) {
                    handleDroppedEvent(data);
                },
                dayHeaderDidMount: function({date, dow, el, view, isToday, ...rest}){
                    console.log('dayHeaderDidMount', {view});

                    const dow_str = dowIntToString(dow);

                    if(view.type === "timeGridWeek"){
                        console.log('dayHeaderDidMount replacing header');
                        let date_str = String(date.getDate());
                        if(date_str.length === 1){
                            date_str = "0"+date_str;
                        }
                        el.innerHTML = `<div class="flex flex-row items-center w-full p-2 font-medium"><span class="text text-sm">${dow_str}</span> <span class="text-3xl font-bold text-secondary flex-grow flex justify-end">${date_str}</span></div>`;
                    } else if (view.type === 'timeGridDay'){
                        console.log('dayHeaderDidMount could replace header here');
                        const dow_or_today = isToday ? 'Today' : dow_str;
                        const date_str = date.toLocaleString();
                        el.innerHTML = `<div class="flex flex-col p-2 text-2xl font-medium"><span class="text text-sm">${dow_or_today}</span> <span class="text-xl font-bold">${date_str}</span></div>`;
                    }
                },
                eventDidMount: function({date, dow, el, view, ...rest}) {
                    console.log({date, dow, el, view, ...rest});
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
            navLinks,
            currentView,
            handleChangeView,
            title
        };
    },
});
</script>

<style>
.fc .fc-toolbar {
    @apply flex-col gap-2;
    @screen lg {
        @apply flex-row;
    }
}
.fc .fc-daygrid-day-bottom{
    @apply text-xs;
}
.fc-theme-standard .fc-popover{
    @apply bg-base-300;
}
.fc-v-event .fc-event-title-container{
    @apply text-xs leading-tight;
}
.fc-v-event .fc-event-main-frame{
    @apply flex-col-reverse;
}
.fc-event-title{
    @apply text-base;
}
.fc-timegrid-event .fc-event-main{
    @apply p-1;
}
.fc .fc-timegrid-slot-minor{
    @apply border-0;
}
.fc .fc-timegrid-slot{
    @apply h-8;
}
.fc .fc-timegrid-slot-label-cushion {
    @apply transform -translate-y-full py-1 uppercase;
}
.fc-theme-standard .fc-scrollgrid{
    @apply border-0;
}
th:last-child{
    @apply border-r-0;
}
.fc .fc-scrollgrid-section-liquid > td{
    @apply border-0;
}
.fc-col-header-cell-cushion {
   @apply text-sm font-medium;
}
/*this breaks first line in month grid view*/
.fc-daygrid-day-top{
    @apply absolute bottom-0 right-0 text-2xl p-2 font-bold;
}
.fc-day-today > div > div > a {
    @apply rounded-full p-4 relative;
/*@apply bg-secondary;*/
    &:before{
        content: '';
        @apply absolute bg-secondary rounded-full h-full w-full z-[-1] inset-0;
    }
}
.fc .fc-daygrid-day-number {
    @apply p-2;
}
.fc-daygrid-day.fc-day-today, .fc-timegrid-col.fc-day-today{
    @apply !bg-transparent;
}
.fc .fc-timegrid-now-indicator-line{
    @apply border-2;
}
</style>
