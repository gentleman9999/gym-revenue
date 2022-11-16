<template>
    <div class="workout-history-container">
        <div class="workout-history-title">Workout History</div>
        <div class="flex flex-col space-y-4">
            <div
                v-for="item in mock"
                :key="item.id"
                class="workout-history-item"
            >
                <div class="uppercase text-sm">{{ item.date }}</div>
                <div class="text-lg font-semibold mt-2">{{ item.title }}</div>
                <div>Trainer {{ item.trainer }}</div>
                <div class="text-secondary uppercase text-sm mt-2 mb-5">
                    {{ item.location }}
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.workout-history-container {
    @apply flex flex-col items-center;
}
.workout-history-title {
    @apply text-xl font-bold mb-3;
}
.workout-history-item {
    @apply flex flex-col w-full border-b border-secondary;
    width: 260px;
}
</style>
<script setup>
import Button from "@/Components/Button.vue";

const lastCheck = "05/02/2022 3:12 pm";
const mock = [
    {
        id: 1,
        date: "May 20, 2022 9:15 am",
        title: "Small Group PT",
        trainer: "Chad Hodges",
        location: "Location one",
    },
    {
        id: 2,
        date: "May 21, 2022 11:00 am",
        title: "GroupX Class",
        trainer: "Roger Wright",
        location: "Location one",
    },
    {
        id: 3,
        date: "May 22, 2022 9:15 am",
        title: "Small Group PT",
        trainer: "Roger Wright",
        location: "Location one",
    },
];
</script>
