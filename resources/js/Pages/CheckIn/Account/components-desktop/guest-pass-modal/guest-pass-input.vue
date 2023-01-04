<template>
    <div class="guest-input-container">
        <div class="text-lg">{{ label }}</div>
        <input class="guest-input" v-bind="props" />
    </div>
</template>
<style scoped>
.guest-input-container {
    @apply flex flex-col space-y-3 w-64;
}
.guest-input {
    @apply h-9 bg-secondary rounded pl-2 outline-none;
}
</style>
<script setup>
const props = defineProps({
    label: String,
    value: String,
});
</script>
