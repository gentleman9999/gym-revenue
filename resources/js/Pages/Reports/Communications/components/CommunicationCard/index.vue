<template>
    <div class="w-full px-2">
        <Card :options="{ more: true }">
            <template #cardTitle>
                <div
                    class="text-base-content text-lg text-bold w-2/3 border-b border-neutral-500"
                >
                    {{ title }}
                </div>
            </template>
            <progress-chart :data="[progress]" />
            <!-- <img :src="progress" class="w-fit m-auto mt-4.5" /> -->
            <div class="btn-action">
                <Button secondary size="xs">Detail</Button>
            </div>
        </Card>
    </div>
</template>
<style scoped>
.btn-action {
    @apply flex flex-row justify-end mt-3;
}
.no-action .btn-action {
    @apply hidden;
}
</style>
<script setup>
import Card from "@/Components/Card";
import Button from "@/Components/Button";

import ProgressChart from "../Charts/ProgressChart";
const props = defineProps({
    title: {
        type: String,
        default: "",
    },
    progress: {
        type: Number,
        default: 0,
    },
});
</script>
