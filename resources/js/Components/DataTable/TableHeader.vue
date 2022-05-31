<template>
    <thead class="sticky top-0 bg-neutral-900 text-white">
        <th
            v-for="column in columns"
            :key="column.field"
            class="px-3"
            :class="column.class + (rowBordered ? ' px-3' : '  px-3 border-b')"
        >
            {{ column.label }}
        </th>
    </thead>
</template>
<style scoped>
thead {
    z-index: 5;
}
</style>
<script setup>
const props = defineProps({
    columns: {
        type: Array,
        default: [],
    },
    rowBordered: {
        type: Boolean,
        default: false,
    },
});
</script>
