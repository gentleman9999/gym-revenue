<template>
    <div
        class="badge"
        :class="{
            'bg-warning': type === 'warning',
            'bg-error': type === 'error',
            'bg-accent': type === 'accent',
            'bg-neutral-500': type === 'neutral',
        }"
    >
        {{ label }}
    </div>
</template>
<style scoped>
.badge {
    @apply rounded-2xl;
}
</style>
<script setup>
const props = defineProps({
    label: {
        type: String,
        default: "",
    },
    type: {
        type: String,
        default: "neutral",
    },
});
</script>
