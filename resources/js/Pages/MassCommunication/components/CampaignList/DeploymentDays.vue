<template>
    <div class="deployment-badge">{{ value }}</div>
    <!--    <Button secondary size="xs">View All</Button>-->
</template>
<style scopded>
.deployment-badge {
    @apply bg-error rounded-full w-7 h-7 flex items-center justify-center;
}
</style>
<script setup>
import Button from "@/Components/Button.vue";
const props = defineProps({
    value: {
        type: Number,
        default: 0,
    },
});
</script>
