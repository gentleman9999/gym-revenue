<template>
    <dashboard-widget title="KPI" :modal-component="KpiModal" :amount="18000">
        <div class="text-secondary">Most Recent Location</div>
        <div>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin quis
            scelerisque lectus.
        </div>
    </dashboard-widget>
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
