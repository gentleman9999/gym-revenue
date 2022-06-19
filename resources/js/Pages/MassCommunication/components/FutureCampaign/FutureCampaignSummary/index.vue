<template>
    <div class="future-summary-wrapper">
        <div class="future-summary-title">Campaign Name-Title</div>
        <div class="future-summary-row">
            <div>Date Range</div>
            <div>July 1- July 23</div>
        </div>
        <div class="future-summary-row">
            <div>Goal %</div>
            <div>0.00%</div>
        </div>
        <div class="future-summary-row">
            <div>Revenue Generated</div>
            <div>TBD</div>
        </div>
        <div class="future-summary-row">
            <div>Total opens</div>
            <div>TBD</div>
        </div>
    </div>
</template>
<style scoped>
.future-summary-wrapper {
    @apply flex flex-col border border-secondary rounded p-5 w-full;
}
.future-summary-title {
    @apply text-lg font-bold;
}
.future-summary-row {
    @apply flex flex-row justify-between;
}
</style>
