<template>
    <div class="flex flex-col">
        <div class="flex flex-row justify-between pb-6">
            <div class="text-xl text-secondary">{{ title }}</div>
            <slot name="filter"></slot>
        </div>
        <div class="flex flex-row flex-wrap">
            <div
                v-for="(card, ndx) in cards"
                :key="ndx"
                class="px-2 mb-2"
                :class="{
                    'md:w-1/3 w-1/2': cards.length === 3,
                    'md:w-1/5 w-1/2': cards.length === 5,
                }"
            >
                <status-card :status="card.status" :count="card.count" />
            </div>
        </div>
    </div>
</template>

<script setup>
import StatusCard from "./StatusCard.vue";
const props = defineProps({
    title: {
        type: String,
        default: "",
    },
    cards: {
        type: Array,
        default: [],
    },
});
</script>
