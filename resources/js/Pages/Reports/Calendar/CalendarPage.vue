<template>
    <app-layout>
        <template #header>
            <calendar-title>
                <template #title>
                    <h1>Calendar Reporting</h1>
                </template>
            </calendar-title>
        </template>
        <div class="px-10 flex flex-col">
            <div class="text-secondary pl-9 text-xl">Client Activity</div>
            <div class="flex flex-row space-x-2 flex-wrap">
                <div class="flex w-full md:w-3/5 flex-wrap">
                    <div class="w-4/5 m-auto md:w-1/3 px-2">
                        <calendar-summary-card
                            title="Confirmed"
                            :progress="[65]"
                        />
                    </div>
                    <div class="w-4/5 m-auto md:w-1/3 px-2">
                        <calendar-summary-card
                            title="Canceled"
                            :progress="[25]"
                        />
                    </div>
                    <div class="w-4/5 m-auto md:w-1/3 px-2">
                        <calendar-summary-card
                            title="Rescheduled"
                            :progress="[10]"
                        />
                    </div>
                </div>
                <calendar-grid />
            </div>

            <calendar-schedule-table :data="schedule" />
        </div>
        <jet-bar-container> </jet-bar-container>
    </app-layout>
</template>

<script setup>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import JetBarContainer from "@/Components/JetBarContainer";
import CalendarScheduleTable from "./components/CalendarScheduleTable";
import CalendarTitle from "../components/CalendarTitle";
import CalendarGrid from "@/Pages/components/CalendarGrid";
import CalendarSummaryCard from "@/Pages/components/CalendarSummaryCard";

const schedule = [
    {
        id: 1,
        location: "Location ONE",
        event: "Welcome Call",
        employee: "/img/avatars/calendar-avatar.png",
        time: "9.00AM",
    },
    {
        id: 2,
        location: "Location TWO",
        event: "Welcome Email",
        employee: "/img/avatars/calendar-avatar.png",
        time: "10.00AM",
    },
    {
        id: 3,
        location: "Location THREE",
        event: "Text",
        employee: "/img/avatars/calendar-avatar.png",
        time: "10.30AM",
    },
    {
        id: 4,
        location: "Location FOUR",
        event: "Team Meeting",
        employee: "/img/avatars/calendar-avatar.png",
        time: "11.00AM",
    },
];
</script>
