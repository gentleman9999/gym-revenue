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
<style scope>
.content-wrapper {
    z-index: 1;
}
.location-wrapper {
    position: absolute;
    right: 24px;
    top: 0px;
    width: calc(100% - 24px);
}
.available-item {
    cursor: pointer;
    user-select: none;
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
