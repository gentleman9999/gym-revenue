<template>
    <Card
        :options="{
            collapse: true,
            borderedTitle: true,
        }"
        class="h-full min-w-fit"
    >
        <template #cardTitle>
            <div class="text-base-content text-lg text-bold">Canceled</div>
        </template>
        <div class="w-40 m-auto">
            <progress-chart :data="[progress]" :height="120" />
        </div>
    </Card>
</template>
<script setup>
import Card from "@/Components/Card.vue";
import ProgressChart from "@/Components/ProgressChart.vue";
const props = defineProps({
    progress: {
        stype: String,
        default: "",
    },
});
</script>
