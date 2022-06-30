<template>
    <select-box :label="value" :items="status" size="xs"></select-box>
</template>
<style scoped></style>
<script setup>
import SelectBox from "@/Components/SelectBox";
const props = defineProps({
    value: {
        type: String,
        default: "In Progress",
    },
});

const status = ["In Progress", "Pause", "Rescheduled", "Completed"];
</script>
