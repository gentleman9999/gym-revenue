<template>
    <div class="checkin-history-container">
        <div class="mb-5 font-semibold">Check In History</div>
        <div class="checkin-history-subtitle">Last Check-in</div>
        <div class="checkin-history-item">{{ lastCheck }}</div>
        <div class="total-checkin-container">
            <div class="text-secondary">Total Check-Ins</div>
            <div class="total-checkin">224</div>
        </div>
        <div class="checkin-history-subtitle">Check-ins</div>
        <div
            class="checkin-history-item"
            v-for="(item, ndx) in mock"
            :key="ndx"
        >
            {{ item }}
        </div>
    </div>
</template>
<style scoped>
.checkin-history-container {
    @apply flex flex-col items-center text-lg;
}
.checkin-history-item {
    @apply flex items-center h-9 my-2 bg-base-content/40 rounded self-start pl-4;
    width: 220px;
}
.checkin-history-subtitle {
    @apply text-secondary self-start;
}
.total-checkin-container {
    @apply flex flex-row justify-between w-full my-3 items-center;
}
.total-checkin {
    @apply bg-secondary rounded text-xl font-semibold px-2 py-1;
}
</style>
<script setup>
import Button from "@/Components/Button.vue";

const lastCheck = "05/02/2022 3:12 pm";
const mock = [
    "05/02/2022 3:12 pm",
    "04/28/2022 5:04 pm",
    "04/25/2022 1:49 pm",
    "05/02/2022 3:12 pm",
    "04/28/2022 5:04 pm",
];
</script>
