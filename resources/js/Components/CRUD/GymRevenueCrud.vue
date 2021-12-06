<template>
    <div class="hidden lg:block">
        <gym-revenue-data-table v-bind="$props"  />
    </div>
    <div class="lg:hidden">
        <gym-revenue-data-cards v-bind="$props"/>
    </div>
    <slot name="pagination">
        <pagination class="mt-4" :links="resource.links" />
    </slot>
</template>

<script>
import Pagination from "@/Components/Pagination";
import GymRevenueDataCards from "./GymRevenueDataCards";
import GymRevenueDataTable from "./GymRevenueDataTable";

export default {
    components: {
        GymRevenueDataCards,
        GymRevenueDataTable,
        Pagination,
    },
    props: {
        fields: {
            type: Array,
        },
        resource: {
            type: Object,
        },
        modelName: {
            type: String,
            required: true,
        },
        modelNamePlural: {
            type: String,
        },
        titleField: {
            type: String,
        },
        cardComponent: {
            type: Object,
        },
        rowComponent: {
            type: Object,
        },
        actions: {
            type: Object,
            default: {},
        },
    },
};
</script>
