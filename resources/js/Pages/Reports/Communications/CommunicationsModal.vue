<template>
    <dashboard-widget-modal
        title="Communications"
        v-bind="$attrs"
        ref="modal"
        page-route="Communications"
        class="max-h-fit"
    >
        <div class="flex flex-row flex-wrap">
            <communication-card
                title="Deliver Rate"
                progress="/img/mocks/clalendar_page_1.png"
                class="md:w-60 no-more no-action"
            />
            <communication-card
                title="Open Rate"
                progress="/img/mocks/clalendar_page_2.png"
                class="md:w-60 no-more no-action"
            />
        </div>
        <div class="flex flex-row flex-wrap">
            <communication-card
                title="Click Rate"
                progress="/img/mocks/clalendar_page_3.png"
                class="md:w-60 no-more no-action"
            />
            <communication-card
                title="Opt-Out Rate"
                progress="/img/mocks/clalendar_page_1.png"
                class="md:w-60 no-more no-action"
            />
        </div>
    </dashboard-widget-modal>
</template>
<script>
import { defineComponent, ref } from "vue";
import DashboardWidgetModal from "@/Pages/Reports/Partials/DashboardWidgetModal";
import CommunicationCard from "./components/CommunicationCard";

export default defineComponent({
    components: {
        DashboardWidgetModal,
        CommunicationCard,
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
