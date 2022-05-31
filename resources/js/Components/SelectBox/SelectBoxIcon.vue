<template>
    <span
        class="transform transition transition-transform ml-2"
        :class="{
            'rotate-0': isCollapsed,
            'rotate-180': !isCollapsed,
        }"
    >
        <arrow-icon
            direction="down"
            :fill="isCollapsed ? color : '#FFF'"
            :size="size"
        />
    </span>
</template>
<script setup>
import ArrowIcon from "@/Components/Icons/Arrow";
const props = defineProps({
    isCollapsed: {
        type: Boolean,
    },
    size: {
        type: String,
        default: "sm",
    },
    color: {
        type: String,
        default: "#FFF",
    },
});
</script>
