<template>
    <Card
        :options="{
            collapse: true,
            borderedTitle: true,
        }"
        class="h-full"
    >
        <template #cardTitle>
            <div class="text-base-content text-lg text-bold">Canceled</div>
        </template>
        <img :src="progress" />
    </Card>
</template>
<script setup>
import Card from "@/Components/Card";

const props = defineProps({
    progress: {
        stype: String,
        default: "",
    },
});
</script>
