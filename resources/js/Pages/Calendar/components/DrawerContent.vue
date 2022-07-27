<template>
    <div class="bg-secondary">
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
