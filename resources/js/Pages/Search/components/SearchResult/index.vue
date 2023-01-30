<template>
    <div class="flex flex-col items-center px-24">
        <search-item-card
            v-for="data in results.data"
            :data="data"
            :key="data.id"
            class="w-full"
        />
        <pagination
            class="mt-4"
            :paginatorInfo="results.paginatorInfo"
            @update-page="$emit('update-page', $event)"
        />
    </div>
</template>
<script setup>
import Pagination from "@/Components/Pagination.vue";
import SearchItemCard from "../SearchItemCard/index.vue";
const props = defineProps({
    results: {
        type: Object,
    },
});
</script>
