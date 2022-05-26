<template>
    <div :class="'kpi-card-wrapper flex w-full px-0 md:px-3 ' + props.class">
        <slot></slot>
    </div>
</template>
<script setup>
const props = defineProps({
    class: {
        type: String,
        default: "md:w-1/2 lg:w-1/3",
    },
});
</script>
