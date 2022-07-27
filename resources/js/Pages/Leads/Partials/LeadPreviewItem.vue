<template>
    <div v-bind="$attrs">
        <div>{{ label }}</div>
        <div class="preview-value">{{ value ? value : "Not Available" }}</div>
    </div>
</template>
<style scoped>
.preview-value {
    @apply bg-secondary text-base-content rounded py-1 mr-2 pl-4;
}
</style>
<script setup>
const props = defineProps({
    label: {
        type: String,
        default: "",
    },
    value: {
        type: String,
        default: "Not Available",
    },
});
</script>
