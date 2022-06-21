<template>
    <dashboard-widget-modal
        title="Sales"
        v-bind="$attrs"
        ref="modal"
        page-route="Sales"
    >
        <div class="flex flex-row">
            <sales-annual-fees />
            <card
                :options="{
                    borderedTitle: true,
                }"
                class="w-1/2 flex flex-col"
            >
                <template #cardTitle>
                    <div class="text-xl text-base-content">
                        Percentage to goal
                    </div>
                </template>
                <div class="flex h-full items-center">
                    <percent2-goal-chart :data="[80]" />
                </div>
            </card>
        </div>
    </dashboard-widget-modal>
</template>
<script>
import { defineComponent, ref } from "vue";
import DashboardWidgetModal from "@/Pages/Reports/Partials/DashboardWidgetModal";
import Card from "@/Components/Card";
import SalesAnnualFees from "./components/SalesAnnualFees";
import SalesPercent2Goal from "./components/SalesPercent2Goal";
import Percent2GoalChart from "./components/SalesPercent2Goal/Percent2GoalChart";

export default defineComponent({
    components: {
        DashboardWidgetModal,
        Card,
        SalesAnnualFees,
        SalesPercent2Goal,
        Percent2GoalChart,
    },
    setup() {
        const modal = ref();

        const open = () => {
            if (modal.value?.open) {
                modal.value?.open();
            }
        };
        const close = () => {
            if (modal.value?.close) {
                modal.value?.close();
            }
        };
        return { open, close, modal };
    },
});
</script>
