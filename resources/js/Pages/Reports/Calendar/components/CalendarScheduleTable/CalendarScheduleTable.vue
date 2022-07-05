<template>
    <Card class="mx-2">
        <table class="text-center w-full">
            <schedule-table-header />
            <schedule-table-body :data="data" />
        </table>
    </Card>
</template>
<style scoped>
table {
    border-spacing: 0 20px;
    @apply w-full border-separate;
}
</style>
<script setup>
import Card from "@/Components/Card.vue";
import ScheduleTableHeader from "./ScheduleTableHeader.vue";
import ScheduleTableBody from "./ScheduleTableBody.vue";

const props = defineProps({
    data: {
        type: Array,
        default: [],
    },
});
</script>
