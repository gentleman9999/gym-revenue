<template>
    <dashboard-widget-modal
        title="Sales Funnel"
        v-bind="$attrs"
        ref="modal"
        page-route="SalesFunnel"
    >
        <div class="flex flex-row space-x-2">
            <cancel-card
                class="no-collapse h-auto"
                progress="/img/mocks/clalendar_page_1.png"
            />
            <completed-card
                class="no-favorite no-collapse no-action"
                progress="/img/mocks/clalendar_page_2.png"
            />
        </div>
        <div>
            <lead-stats-card
                class="no-favorite no-collapse"
                progress="/img/mocks/clalendar_page_3.png"
            />
        </div>
    </dashboard-widget-modal>
</template>
<script>
import { defineComponent, ref } from "vue";
import DashboardWidgetModal from "@/Pages/Reports/Partials/DashboardWidgetModal";

import CancelCard from "./components/CancelCard";
import CompletedCard from "./components/CompletedCard";
import LeadStatsCard from "./components/LeadStatsCard";

export default defineComponent({
    components: {
        DashboardWidgetModal,
        CancelCard,
        CompletedCard,
        LeadStatsCard,
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
