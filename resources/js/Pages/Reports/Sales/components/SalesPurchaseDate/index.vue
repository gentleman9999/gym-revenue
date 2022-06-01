<template>
    <sales-card class="w-full">
        <template #cardTitle>
            <div
                class="text-base-content w-full text-lg text-bold border-b border-neutral-500"
            >
                Purchase Date
            </div>
        </template>
        <div class="flex flex-col w-full items-end px-3 mb-5 mt-8">
            <div class="flex flex-row w-full justify-between mb-4">
                <span class="text-secondary text-base">From:</span>
                <span
                    class="flex w-max space-x-2 text-base-content text-sm leading-6"
                >
                    <calendar-icon />
                    &nbsp;&nbsp;&nbsp;2022-01-09
                </span>
            </div>
            <div class="flex flex-row w-full justify-between mb-8">
                <span class="text-secondary text-base">To:</span>
                <span
                    class="flex w-max space-x-2 text-base-content text-sm leading-6"
                >
                    <calendar-icon />
                    &nbsp;&nbsp;&nbsp;2022-01-23
                </span>
            </div>
            <Button size="xs" secondary>Set</Button>
        </div>
    </sales-card>
</template>
<script setup>
import { ref } from "vue";
import DatePicker from "vue3-date-time-picker";
import SalesCard from "@/Components/Card";
import CalendarIcon from "@/Components/Icons/Calendar";
import Button from "@/Components/Button";
</script>
