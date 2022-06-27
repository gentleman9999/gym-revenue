<template>
    <div class="flex flex-col">
        <date-picker
            inline
            autoApply
            dark
            :enableTimePicker="false"
            :month-change-on-scroll="false"
        />
        <div class="flex flex-row justify-between pt-5">
            <month-selector />
            <location-selector />
        </div>
    </div>
</template>

<script setup>
import MonthSelector from "./MonthSelector";
import LocationSelector from "./LocationSelector";
import DatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
</script>
