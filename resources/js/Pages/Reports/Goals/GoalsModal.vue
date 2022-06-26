<template>
    <dashboard-widget-modal
        title="Goals"
        v-bind="$attrs"
        ref="modal"
        page-route="Goals"
    >
        <div class="flex flex-row">
            <goals-actual-card class="mr-2" />
            <goals-projecting-card class="w-64" />
        </div>
    </dashboard-widget-modal>
</template>
<script>
import { defineComponent, ref } from "vue";
import DashboardWidgetModal from "@/Pages/Reports/Partials/DashboardWidgetModal";
import GoalsActualCard from "./components/GoalsActualCard";
import GoalsProjectingCard from "./components/GoalsProjectingCard";

export default defineComponent({
    components: {
        DashboardWidgetModal,
        GoalsActualCard,
        GoalsProjectingCard,
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
