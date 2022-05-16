<template>
    <dashboard-widget
        title="Communications"
        :modal-component="CommunicationsModal"
    ></dashboard-widget>
</template>

<script>
import { defineComponent } from "vue";
import DashboardWidget from "@/Pages/Reports/Partials/DashboardWidget";
import CommunicationsModal from "@/Pages/Reports/Communications/CommunicationsModal";

export default defineComponent({
    components: {
        DashboardWidget,
    },
    setup() {
        return { CommunicationsModal };
    },
});
</script>
