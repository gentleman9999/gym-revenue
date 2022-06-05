<template>
    <div class="task-date-switcher">
        <date-switcher-card
            v-for="(date, ndx) in daysOfWeek"
            :key="ndx"
            :date="date"
            :ndx="ndx"
            :active="currentDate === date"
            @click="() => handleCardClick(ndx)"
        />
    </div>
</template>
<style scoped>
.task-date-switcher {
    @apply flex flex-row space-x-4 px-4;
}
</style>
<script setup>
import { ref, computed } from "vue";
import DateSwitcherCard from "./DateSwitcherCard";
const props = defineProps({
    selectedDate: {
        type: Date,
        default: new Date(),
    },
    startOfTheWeek: {
        type: Date,
        default: new Date(),
    },
    setSelectedDate: {
        type: Function,
    },
    setStartOfTheWeek: {
        type: Function,
    },
});

const handleCardClick = (ndx) => {
    let current = new Date(props.startOfTheWeek);
    current.setDate(current.getDate() + ndx);
    props.setSelectedDate(current);
};

const daysOfWeek = computed({
    get() {
        let week = [];
        let current = new Date(props.startOfTheWeek);
        for (var i = 0; i < 7; i++) {
            week.push(new Date(current).getDate());
            current.setDate(current.getDate() + 1);
        }
        return week;
    },
});

const currentDate = computed({
    get() {
        return props.selectedDate.getDate();
    },
});
</script>
