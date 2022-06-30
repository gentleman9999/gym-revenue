<template>
    <dashboard-widget
        title="Sales"
        :modal-component="SalesModal"
        :amount="18000"
    >
        <div class="flex flex-row">
            <div class="w-1/3 text-secondary flex flex-col gap-2 items-center">
                <div>Stat 1</div>
                <div>Stat 2</div>
                <div>Stat 3</div>
                <div>Stat 4</div>
            </div>
            <div class="w-2/3">
                <div class="w-32">
                    <sales-widget-chart :height="140" />
                </div>
            </div>
        </div>

        <div class="text-secondary font-bold">Total: 100%</div>
    </dashboard-widget>
</template>

<script>
import { defineComponent } from "vue";
import DashboardWidget from "@/Pages/Reports/Partials/DashboardWidget.vue";
import SalesModal from "@/Pages/Reports/Sales/SalesModal.vue";
import SalesWidgetChart from "./components/SalesWidgetChart.vue";
export default defineComponent({
    components: {
        DashboardWidget,
        SalesWidgetChart,
    },
    setup() {
        return { SalesModal };
    },
});
</script>
