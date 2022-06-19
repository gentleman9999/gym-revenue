<template>
    <div class="conversion-data-wrapper">
        <div class="conversion-data-header">
            <div class="conversion-data-title">Conversion Data</div>
            <conversion-filters />
        </div>
        <div class="conversion-data-body">
            <div class="flex flex-col">April</div>
        </div>
    </div>
</template>
<style scoped>
.conversion-data-wrapper {
    @apply flex flex-col mt-12;
}
.conversion-data-header {
    @apply flex flex-row justify-between;
}
.conversion-data-title {
    @apply text-xl font-bold;
}
</style>
<script setup>
import ConversionFilters from "./ConversionFilters";
</script>
