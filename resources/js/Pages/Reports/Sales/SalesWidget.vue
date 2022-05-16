<template>
    <dashboard-widget
        title="Sales"
        :modal-component="SalesModal"
    ></dashboard-widget>
</template>

<script>
import { defineComponent } from "vue";
import DashboardWidget from "@/Pages/Reports/Partials/DashboardWidget";
import SalesModal from "@/Pages/Reports/Sales/SalesModal";

export default defineComponent({
    components: {
        DashboardWidget,
    },
    setup() {
        return { SalesModal };
    },
});
</script>
