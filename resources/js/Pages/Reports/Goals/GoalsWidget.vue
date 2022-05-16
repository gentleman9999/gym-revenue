<template>
    <dashboard-widget
        title="Goals"
        :modal-component="GoalsModal"
    ></dashboard-widget>
</template>

<script>
import { defineComponent } from "vue";
import DashboardWidget from "@/Pages/Reports/Partials/DashboardWidget";
import GoalsModal from "@/Pages/Reports/Goals/GoalsModal";

export default defineComponent({
    components: {
        DashboardWidget,
    },
    setup() {
        return { GoalsModal };
    },
});
</script>
