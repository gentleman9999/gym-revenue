<template>
    <div class="flex flex-col w-1/2 relative">
        <div class="overflow-y-scroll h-60 space-y-2 content-wrapper pl-2 pt-2">
            <div
                class="available-item"
                v-for="(item, index) in props.data"
                :key="index"
                :onClick="() => toggleSelect(item)"
            >
                {{ item }}
            </div>
        </div>
        <div class="location-wrapper border rounded h-60"></div>
    </div>
</template>
<style scoped>
.content-wrapper {
    @apply z-1;
}
.location-wrapper {
    width: calc(100% - 24px);
    @apply absolute right-6 top-0;
}
.available-item {
    @apply cursor-pointer select-none text-base-content;
}
</style>
<script setup>
const props = defineProps({
    data: {
        type: Array,
        default: [],
    },
    toggleSelect: {
        type: Function,
    },
});
</script>
