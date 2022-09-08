<template>
    <div class="goal-filter-wrapper">
        <div class="goal-filters">
            <select-box
                :items="periods"
                label="Time Period"
                size="xs"
                color="text-base-content"
            />
            <select-box :items="locations" label="Location" size="xs" />
            <select-box :items="employees" label="Employee" size="xs" />
            <select-box :items="progress" label="In Progress" size="xs" />
        </div>
        <div class="goal-filter-operations">
            <Button secondary size="xs">+ New</Button>
            <select-box :items="goals" label="Search Goals" size="xs" outline />
        </div>
    </div>
</template>
<style>
.goal-filter-wrapper {
    @apply flex flex-row justify-between w-full flex-wrap;
}
.goal-filter-wrapper .btn-secondary {
    @apply text-base-content border-secondary rounded;
}
.goal-filters {
    @apply flex flex-row md:space-x-6 flex-wrap;
}
.goal-filters > div {
    @apply mt-1 mr-2;
}
.goal-filter-operations {
    @apply flex flex-row md:space-x-6 space-x-2 items-baseline mt-1;
}
</style>
<script setup>
import SelectBox from "@/Components/SelectBox";
import Button from "@/Components/Button.vue";

const periods = new Array(6).fill("Time Period");
const locations = new Array(6).fill("Location");
const employees = new Array(6).fill("Employees");
const progress = new Array(6).fill("In Progress");
const goals = [
    "Personal Training Package Solid",
    "Personal Training Revenue",
    "Membership Revenue",
    "Calls By location",
];
</script>
