<template>
    <Card
        :options="{
            borderedTitle: true,
            favorite: favorite,
            collapse: collapse,
        }"
    >
        <template #cardTitle>
            <div class="text-base-content text-lg text-bold">
                {{ title }}
            </div>
        </template>
        <div class="flex flex-col">
            <div class="w-48">
                <progress-chart :data="progress" />
            </div>
            <Button v-if="showAction" size="xs" secondary class="self-end"
                >Detail</Button
            >
        </div>
    </Card>
</template>
<script setup>
import Card from "@/Components/Card.vue";
import Button from "@/Components/Button.vue";

import ProgressChart from "@/Components/ProgressChart.vue";
const props = defineProps({
    title: {
        type: String,
        default: "",
    },
    progress: {
        type: Array,
        default: [],
    },
    favorite: {
        type: Boolean,
        default: true,
    },
    collapse: {
        type: Boolean,
        default: true,
    },
    showAction: {
        type: Boolean,
        default: true,
    },
});
</script>
