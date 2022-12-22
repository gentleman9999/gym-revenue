<template>
    <div class="date-range-container">
        <div>Date Range</div>
        <div class="date-range-radio-group">
            <label
                class="date-range-radio-container"
                v-for="item in items"
                :key="item.value"
            >
                <input
                    type="radio"
                    name="date_range"
                    class="date-range-radio"
                    :value="item.value"
                    v-bind="props"
                />
                <span class="label-text">{{ item.label }}</span>
            </label>
        </div>
    </div>
</template>
<style scoped>
.date-range-radio {
    @apply radio checked:bg-secondary;
}
.date-range-container {
    @apply flex flex-col space-y-4 w-64;
}
.date-range-radio-group {
    @apply flex flex-wrap flex-row ml-0;
}
.date-range-radio-container {
    @apply w-1/2 space-x-2 flex flex-row items-center mb-3;
}
</style>
<script setup>
const props = defineProps({
    value: String,
});

const items = [
    {
        label: "No Pass",
        value: "no-pass",
    },
    {
        label: "1-day pass",
        value: "pass-1",
    },
    {
        label: "3-day pass",
        value: "pass-3",
    },
    {
        label: "5-day pass",
        value: "pass-5",
    },
];
</script>
