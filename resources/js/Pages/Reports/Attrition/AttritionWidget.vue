<template>
    <dashboard-widget
        title="Attrition"
        :modal-component="AttritionModal"
    ></dashboard-widget>
</template>

<script>
import { defineComponent } from "vue";
import DashboardWidget from "@/Pages/Reports/Partials/DashboardWidget";
import AttritionModal from "@/Pages/Reports/Attrition/AttritionModal";

export default defineComponent({
    components: {
        DashboardWidget,
    },
    setup() {
        return { AttritionModal };
    },
});
</script>
