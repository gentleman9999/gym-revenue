<template>
    <dashboard-widget
        title="Index"
        :modal-component="IndexModal"
    ></dashboard-widget>
</template>

<script>
import { defineComponent } from "vue";
import DashboardWidget from "@/Pages/Reports/Partials/DashboardWidget";
import IndexModal from "@/Pages/Reports/Index/IndexModal";

export default defineComponent({
    components: {
        DashboardWidget,
    },
    setup() {
        return { IndexModal };
    },
});
</script>
