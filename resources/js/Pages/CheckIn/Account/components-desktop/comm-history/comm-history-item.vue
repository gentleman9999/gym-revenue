<template>
    <div class="comm-history-item">
        <div class="flex flex-col">
            <div>{{ data.content }}</div>
            <div class="comm-history-date">{{ data.date }}</div>
        </div>
        <div class="comm-history-item-action">
            <div class="comm-history-item-status">{{ data.status }}</div>
            <Button secondary size="sm">View</Button>
        </div>
    </div>
</template>
<style scoped>
.comm-history-item {
    @apply flex flex-row w-full justify-between border-b pb-2 pt-4  border-neutral-content/50;
    &-status {
        @apply text-sm bg-secondary/20 rounded py-1 w-20 text-center;
    }
    &-action {
        @apply flex flex-row space-x-2 items-center;
    }
}
.comm-history-date {
    @apply text-sm text-base-content/50;
}
</style>
<script setup>
import Button from "@/Components/Button.vue";
const props = defineProps({
    data: Object,
});
</script>
