<template>
    <div class="flex flex-col w-1/2 relative">
        <div
            class="block overflow-y-scroll h-60 space-y-2 content-wrapper pt-2"
        >
            <div
                class="selected-item"
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

.selected-item {
    width: calc(50% - 24px);
    @apply bg-slate-100 ml-2 text-center cursor-pointer select-none border rounded h-fit text-neutral/90 inline-block;
}

@media only screen and (max-width: 1024px) {
    .selected-item {
        width: auto;
    }
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
