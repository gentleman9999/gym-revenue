<template>
    <thead class="sticky top-0 bg-neutral-900 text-white">
        <th
            v-for="column in columns"
            :key="column.field"
            class="px-3"
            :class="column.class"
        >
            {{ column.label }}
        </th>
    </thead>
</template>
<style scoped>
thead {
    border-bottom: 1px solid #868686;
    z-index: 5;
}
</style>
<script setup>
const props = defineProps({
    columns: {
        type: Array,
        default: [],
    },
});
</script>
