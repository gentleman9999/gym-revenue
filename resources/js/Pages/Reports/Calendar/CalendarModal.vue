<template>
    <dashboard-widget-modal
        title="Calendar Reporting"
        v-bind="$attrs"
        ref="modal"
        page-route="Calendar"
    >
        <div class="flex md:flex-row md:space-x-2 flex-col">
            <calendar-summary-card
                title="Confirmed"
                :progress="[65]"
                :favorite="false"
                :collapse="false"
                :showAction="false"
            />
            <calendar-summary-card
                title="Canceled"
                :progress="[25]"
                :favorite="false"
                :collapse="false"
                :showAction="false"
            />
            <calendar-summary-card
                title="Rescheduled"
                :progress="[10]"
                :favorite="false"
                :collapse="false"
                :showAction="false"
            />
        </div>
    </dashboard-widget-modal>
</template>
<script>
import { defineComponent, ref } from "vue";
import DashboardWidgetModal from "@/Pages/Reports/Partials/DashboardWidgetModal.vue";
import CalendarSummaryCard from "@/Pages/components/CalendarSummaryCard.vue";

export default defineComponent({
    components: {
        DashboardWidgetModal,
        CalendarSummaryCard,
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
