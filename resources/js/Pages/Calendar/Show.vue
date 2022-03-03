<template>
    <app-layout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">Calendar</h2>
        </template>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">

            <div class="flex flex-row col-span-3  lg:col-span-2 gap-2">
                <button class="btn btn-sm text-xs"
                        @click="$inertia.visit(route('calendar.create'))"
                >
                    New Event
                </button>
            </div>

            <FullCalendar :options="calendarOptions" />
        </div>

    </app-layout>
</template>

<style scoped>
td > div {
    @apply h-16;
}
</style>

<script>
import { defineComponent, watchEffect, ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import GymRevenueCrud from "@/Components/CRUD/GymRevenueCrud";
import SweetModal from "@/Components/SweetModal3/SweetModal";
import { Inertia } from "@inertiajs/inertia";
import '@fullcalendar/core/vdom' // solves problem with Vite
import FullCalendar, { CalendarOptions, EventApi, DateSelectArg, EventClickArg } from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'

export default defineComponent({
    components: {
        AppLayout,
        GymRevenueCrud,
        SweetModal,
        FullCalendar,
    },
    props: ["sessions", "events", "title", "isClientUser", "filters"],

    setup(props) {


        return {
            Inertia,
            calendarOptions: {
                plugins: [ dayGridPlugin, timeGridPlugin, interactionPlugin],
                initialView: 'dayGridMonth',
                events: props.events,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                editable: true,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                weekends: true,
                select: function(data) {
                    console.log('select. '+data);
                },
                eventClick: function(data) {
                    data.jsEvent.preventDefault(); // don't let the browser navigate
                    console.log('event clicked: '+data.event.title);

                    if (data.event.url) {
                        window.open(data.event.url);
                    }
                },
            }
        }
    }
});
</script>
