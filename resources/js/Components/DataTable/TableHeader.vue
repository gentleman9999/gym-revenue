<template>
    <thead class="sticky top-0 z-10 border-b bg-neutral-900 text-white">
        <th v-for="column in columns" :key="column.field" :class="column.class">
            {{ column.label }}
        </th>
    </thead>
</template>
<style scoped>
thead {
    border-color: #868686;
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
