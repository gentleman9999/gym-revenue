<template>
    <jet-bar-stat-card :title="title" :number="value" :type="type">
        <template v-slot:icon>
            <jet-bar-icon :type="icon" fill />
        </template>
    </jet-bar-stat-card>
</template>

<script setup>
import JetBarStatCard from "@/Components/JetBarStatCard.vue";
import JetBarIcon from "@/Components/JetBarIcon.vue";

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    value: {
        type: Number,
        required: true,
    },
    type: {
        type: String,
        required: true,
    },
    icon: {
        type: String,
        required: true,
    },
});
</script>
