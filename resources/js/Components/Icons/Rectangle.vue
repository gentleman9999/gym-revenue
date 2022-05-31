<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        width="17"
        height="17"
        viewBox="0 0 17 17"
    >
        <rect width="17" height="17" :fill="fill" />
    </svg>
</template>
<script setup>
const props = defineProps({
    fill: {
        type: String,
        default: "#005CA0",
    },
});
// #003A73
</script>
