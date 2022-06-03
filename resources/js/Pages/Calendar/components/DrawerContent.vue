<template>
    <div class="bg-neutral-900">
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
