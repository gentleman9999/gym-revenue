<template>
    <div class="conversion-data-wrapper">
        <div class="conversion-data-header">
            <div class="conversion-data-title">Conversion Data</div>
            <conversion-filters />
        </div>
        <div class="conversion-data-body">
            <div class="conversion-data-details w-2/3">
                <div class="conversion-data-date">April</div>
                <conversion-data-table />
            </div>
            <div class="w-1/3 pl-6">
                <conversion-summary />
            </div>
        </div>
    </div>
</template>
<script setup>
import ConversionFilters from "./ConversionFilters";
import ConversionSummary from "./ConversionSummary";
import ConversionDataTable from "./ConversionDataTable";
</script>
<style scoped>
.conversion-data-wrapper {
    @apply flex flex-col mt-12;
}
.conversion-data-header {
    @apply flex flex-row justify-between pb-3;
}
.conversion-data-title {
    @apply text-xl font-bold;
}
.conversion-data-body {
    @apply flex flex-row border border-secondary justify-between rounded p-3;
}
.conversion-data-details {
    @apply flex flex-col;
}
.conversion-data-date {
    @apply text-xl;
}
</style>
