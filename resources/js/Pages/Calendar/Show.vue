<template>
    <app-layout :title="title">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">Calendar</h2>
        </template>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
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
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'

export default defineComponent({
    components: {
        AppLayout,
        GymRevenueCrud,
        SweetModal,
        FullCalendar,
    },
    props: ["sessions", "events", "title", "isClientUser", "filters"],

    setup(props) {
        const selectedFile = ref(null);
        const modal = ref(null);

        watchEffect(() => {
            if (selectedFile.value) {
                modal.value.open();
            }
        });
        return {
            modal,
            Inertia,
            calendarOptions: {
                plugins: [ dayGridPlugin, timeGridPlugin],
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
            }
        };
    },
});
</script>
