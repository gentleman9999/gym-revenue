<template>
    <div class="flex flex-col w-1/2 relative">
        <div
            class="block overflow-y-scroll h-60 space-y-2 content-wrapper pt-2"
        >
            <div
                class="inline-block selected-item border rounded h-fit text-neutral-900"
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
.selected-item {
    background: #f1f1f1;
    cursor: pointer;
    user-select: none;
    text-align: center;
    width: calc(50% - 20px);
    margin-left: 8px;
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
