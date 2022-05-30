<template>
    <Card>
        <table class="text-center w-full">
            <schedule-table-header />
            <schedule-table-body :data="data" />
        </table>
    </Card>
</template>
<style scoped>
table {
    border-collapse: separate;
    border-spacing: 0 20px;
    width: 100%;
}
</style>
<script setup>
import Card from "@/Components/Card";
import ScheduleTableHeader from "./ScheduleTableHeader";
import ScheduleTableBody from "./ScheduleTableBody";

const props = defineProps({
    data: {
        type: Array,
        default: [],
    },
});
</script>
