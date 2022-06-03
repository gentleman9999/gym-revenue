<template>
    <dashboard-widget-modal
        title="Calendar Reporting"
        v-bind="$attrs"
        ref="modal"
        page-route="Calendar"
    >
        <div class="flex flex-row space-x-2">
            <calendar-summary-card
                title="Confirmed"
                diagram="/img/mocks/clalendar_page_1.png"
                :favorite="false"
                :collapse="false"
                :showAction="false"
            />
            <calendar-summary-card
                title="Canceled"
                diagram="/img/mocks/clalendar_page_2.png"
                :favorite="false"
                :collapse="false"
                :showAction="false"
            />
            <calendar-summary-card
                title="Rescheduled"
                diagram="/img/mocks/clalendar_page_3.png"
                :favorite="false"
                :collapse="false"
                :showAction="false"
            />
        </div>
    </dashboard-widget-modal>
</template>
<script>
import { defineComponent, ref } from "vue";
import DashboardWidgetModal from "@/Pages/Reports/Partials/DashboardWidgetModal";
import CalendarSummaryCard from "./components/CalendarSummaryCard";

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
