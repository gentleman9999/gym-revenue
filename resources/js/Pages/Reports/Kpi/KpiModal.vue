<template>
    <dashboard-widget-modal
        title="KPI"
        v-bind="$attrs"
        ref="modal"
        page-route="Kpi"
    >
        <div class="flex flex-row">
            <kpi-revenue-per-member-overall
                class="lg:w-1/2 md:w-1/2 no-favorite no-collapse"
            />
            <kpi-personal-training
                class="lg:w-1/2 md:w-1/2 no-favorite no-collapse"
            />
        </div>
        <kpi-attachment-rate
            class="lg:w-full no-favorite no-collapse no-select"
        />
    </dashboard-widget-modal>
</template>
<script>
import { defineComponent, ref } from "vue";
import DashboardWidgetModal from "@/Pages/Reports/Partials/DashboardWidgetModal.vue";

import KpiRevenuePerMemberOverall from "./components/KpiRevenuePerMemberOverall/KpiRevenuePerMemberOverall.vue";
import KpiPersonalTraining from "./components/KpiPersonalTraining/KpiPersonalTraining.vue";
import KpiAttachmentRate from "./components/KpiOtherServices/AttachmentRate.vue";

export default defineComponent({
    components: {
        DashboardWidgetModal,
        KpiRevenuePerMemberOverall,
        KpiPersonalTraining,
        KpiAttachmentRate,
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
