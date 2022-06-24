<template>
    <Card>
        <div class="grid-wrapper">
            <date-picker inline autoApply dark :enableTimePicker="false" />
        </div>
    </Card>
</template>
<style scoped>
.grid-wrapper {
    margin-top: -12px;
    margin-bottom: -12px;
    @apply flex flex-col;
}
</style>
<script setup>
import Card from "@/Components/Card";
import ArrowIcon from "@/Components/Icons/Arrow";
import DatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
</script>
