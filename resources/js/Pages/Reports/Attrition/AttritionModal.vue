<template>
    <dashboard-widget-modal
        title="Attrition"
        v-bind="$attrs"
        ref="modal"
        page-route="Attrition"
    >
    </dashboard-widget-modal>
</template>
<script>
import { defineComponent, ref } from "vue";
import DashboardWidgetModal from "@/Pages/Reports/Partials/DashboardWidgetModal";

export default defineComponent({
    components: {
        DashboardWidgetModal,
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
