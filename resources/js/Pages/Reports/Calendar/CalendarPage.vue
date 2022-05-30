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
            <div class="flex flex-row space-x-2">
                <div class="flex w-3/5 space-x-2">
                    <calendar-summary-card
                        title="Confirmed"
                        diagram="/img/mocks/clalendar_page_1.png"
                        class="w-1/3"
                    />
                    <calendar-summary-card
                        title="Canceled"
                        diagram="/img/mocks/clalendar_page_2.png"
                        class="w-1/3"
                    />
                    <calendar-summary-card
                        title="Rescheduled"
                        diagram="/img/mocks/clalendar_page_3.png"
                        class="w-1/3"
                    />
                </div>
            </div>
        </div>
        <jet-bar-container> </jet-bar-container>
    </app-layout>
</template>

<script setup>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import JetBarContainer from "@/Components/JetBarContainer";
import CalendarTitle from "../components/CalendarTitle";
import CalendarSummaryCard from "./components/CalendarSummaryCard";
</script>
