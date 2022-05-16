<template>
    <dashboard-widget
        title="KPI"
        :modal-component="KpiModal"
    ></dashboard-widget>
</template>

<script>
import { defineComponent } from "vue";
import DashboardWidget from "@/Pages/Reports/Partials/DashboardWidget";
import KpiModal from "@/Pages/Reports/Kpi/KpiModal";

export default defineComponent({
    components: {
        DashboardWidget,
    },
    setup() {
        return { KpiModal };
    },
});
</script>
