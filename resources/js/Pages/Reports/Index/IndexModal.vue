<template>
    <dashboard-widget-modal
        title="Index"
        v-bind="$attrs"
        ref="modal"
        page-route="Index"
    >
        <div class="flex flex-row space-x-2">
            <cpa-cpl-clicks
                class="flex flex-col no-favorite no-collapse h-[350px]"
            />
            <conversion-rate-card class="flex flex-col no-more h-[350px]" />
        </div>
        <email-stats-card class="no-favorite no-collapse" />
    </dashboard-widget-modal>
</template>
<script>
import { defineComponent, ref } from "vue";
import DashboardWidgetModal from "@/Pages/Reports/Partials/DashboardWidgetModal";

import EmailStatsCard from "./components/EmailStatsCard";
import CpaCplClicks from "./components/CpaCplClicks";
import ConversionRateCard from "./components/ConversionRateCard";

export default defineComponent({
    components: {
        DashboardWidgetModal,
        EmailStatsCard,
        CpaCplClicks,
        ConversionRateCard,
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
