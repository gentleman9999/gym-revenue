<template>
    <dashboard-widget-modal
        title="Sales Funnel"
        v-bind="$attrs"
        ref="modal"
        page-route="SalesFunnel"
    >
        <div class="flex md:flex-row md:space-x-2 flex-col">
            <cancel-card class="no-collapse h-auto" progress="25" />
            <completed-card class="no-favorite no-collapse no-action" />
        </div>
        <div>
            <lead-stats-card class="no-favorite no-collapse" progress="30" />
        </div>
    </dashboard-widget-modal>
</template>
<script>
import { defineComponent, ref } from "vue";
import DashboardWidgetModal from "@/Pages/Reports/Partials/DashboardWidgetModal.vue";

import CancelCard from "./components/CancelCard.vue";
import CompletedCard from "./components/CompletedCard/CompletedCard.vue";
import LeadStatsCard from "./components/LeadStatsCard.vue";

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
