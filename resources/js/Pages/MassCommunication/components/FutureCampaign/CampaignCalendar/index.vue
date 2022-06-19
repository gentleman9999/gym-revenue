<template>
    <div class="flex flex-col">
        <img src="/img/mocks/mass_page_calendar.png" />
        <div class="flex flex-row justify-between pt-5">
            <month-selector />
            <location-selector />
        </div>
    </div>
</template>

<script setup>
import MonthSelector from "./MonthSelector";
import LocationSelector from "./LocationSelector";
</script>
