<template>
    <div class="transaction-log-item">
        <div class="flex flex-col">
            <div>{{ data.title }}</div>
            <div class="transaction-log-count">
                {{ data.count }} transactions
            </div>
        </div>
        <div class="transaction-log-item-status">
            <div>${{ data.total_usd }}</div>
            <div class="text-base-content/60">{{ data.total_percent }}%</div>
        </div>
    </div>
</template>
<style scoped>
.transaction-log-item {
    @apply flex flex-row w-full justify-between border-b pb-2 pt-4  border-neutral-content/50;
    &-status {
        @apply text-sm bg-secondary/20 rounded py-1 w-20 text-right pr-2;
    }
}
.transaction-log-count {
    @apply text-sm text-base-content/50;
}
</style>
<script setup>
const props = defineProps({
    data: Object,
});
</script>
