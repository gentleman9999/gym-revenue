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
            <progress-chart :data="progress" />
            <Button v-if="showAction" size="xs" secondary class="self-end"
                >Detail</Button
            >
        </div>
    </Card>
</template>
<script setup>
import Card from "@/Components/Card";
import Button from "@/Components/Button";

import ProgressChart from "@/Components/ProgressChart";
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
