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
            <img :src="diagram" class="px-5" />
            <Button v-if="showAction" size="xs" secondary class="self-end"
                >Detail</Button
            >
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
