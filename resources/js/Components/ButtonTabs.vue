<template>
    <div class="flex flex-row justify-between mt-2 px-1.5">
        <div
            v-for="(item, index) in items"
            :key="index"
            :class="getClassName(index)"
        >
            {{ item }}
        </div>
    </div>
</template>
<style scoped></style>
<script setup>
const props = defineProps({
    items: {
        type: Array,
        default: ["Membership", "Personal Training", "Retail Sales"],
    },
    activeIndex: {
        type: Number,
        default: 0,
    },
});
const getClassName = (ndx) =>
    "text-white cursor-pointer p-1 rounded " +
    (ndx === props.activeIndex ? "bg-secondary" : "hover:text-secondary");
</script>
