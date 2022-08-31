<template>
    <div class="drawer-wrapper">
        <slot name="eventForm"></slot>
    </div>
</template>
<style scoped>
.drawer-wrapper {
    @apply bg-secondary;
}
</style>
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
