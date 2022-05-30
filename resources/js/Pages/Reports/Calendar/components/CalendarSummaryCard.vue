<template>
    <Card
        :options="{
            borderedTitle: true,
            favorite: true,
            collapse: true,
        }"
    >
        <template #cardTitle>
            <div class="text-white text-lg text-bold">
                {{ title }}
            </div>
        </template>
        <div class="flex flex-col">
            <img :src="diagram" class="px-5" />
            <Button size="xs" secondary class="self-end">Detail</Button>
        </div>
    </Card>
</template>
<script setup>
import Card from "@/Components/Card";
import Button from "@/Components/Button";
const props = defineProps({
    title: {
        type: String,
        default: "",
    },
    diagram: {
        type: String,
        default: "",
    },
});
</script>
