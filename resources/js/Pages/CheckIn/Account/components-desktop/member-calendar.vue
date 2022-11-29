<template>
    <div class="border border-secondary rounded">
        <div class="px-2 relative">
            <div class="member-calendar-title">
                <div class="text-lg font-semibold">
                    <div>Today</div>
                    <div>May 20, 2022</div>
                </div>
                <Button secondary size="sm">Schedule</Button>
            </div>
            <div class="account-calendar-add">
                <font-awesome-icon :icon="['fas', 'plus']" />
            </div>
            <FullCalendar
                :options="calendarOptions"
                ref="calendar"
                class="h-96"
            />
        </div>
    </div>
</template>
<style scoped>
.member-calendar-title {
    @apply flex flex-row justify-between items-center mt-4 pl-6 pr-9;
}
.account-calendar-add {
    @apply flex items-center justify-center border-2 rounded-full w-6 h-6 absolute right-2 top-0;
}
</style>
<script setup>
import Button from "@/Components/Button.vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faPlus } from "@fortawesome/pro-solid-svg-icons";
import FullCalendar from "@fullcalendar/vue3";
import timeGridPlugin from "@fullcalendar/timegrid";

library.add(faPlus);

const calendarOptions = {
    schedulerLicenseKey: "0157232768-fcs-1652392378",
    plugins: [timeGridPlugin],
    initialView: "timeGridDay",
    headerToolbar: {
        left: "",
        center: "",
        right: "",
    },
    views: {
        timeGridDay: {
            dayHeaderFormat: {
                month: "short",
                day: "numeric",
                omitCommas: "false",
            },
            nowIndicator: true,
            allDaySlot: false,
        },
    },
};
</script>
