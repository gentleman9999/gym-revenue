<template>
    <dashboard-widget
        title="Sales Funnel"
        :modal-component="SalesFunnelModal"
    ></dashboard-widget>
</template>

<script>
import { defineComponent } from "vue";
import DashboardWidget from "@/Pages/Reports/Partials/DashboardWidget";
import SalesFunnelModal from "@/Pages/Reports/SalesFunnel/SalesFunnelModal";

export default defineComponent({
    components: {
        DashboardWidget,
    },
    setup() {
        return { SalesFunnelModal };
    },
});
</script>
