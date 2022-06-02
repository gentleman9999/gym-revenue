<template>
    <div class="bg-neutral-900">
        <h1 class="font-bold mb-4">{{ title }}</h1>
        <slot name="eventForm"></slot>
    </div>
</template>
<script setup>
const props = defineProps({
    title: {
        type: String,
        default: "",
    },
    toggleSwitch: {
        type: Function,
    },
});
</script>
