<template>
    <dashboard-widget
        title="Calendar Reporting"
        :modal-component="CalendarModal"
    ></dashboard-widget>
</template>

<script>
import { defineComponent } from "vue";
import DashboardWidget from "@/Pages/Reports/Partials/DashboardWidget";
import CalendarModal from "@/Pages/Reports/Calendar/CalendarModal";

export default defineComponent({
    components: {
        DashboardWidget,
    },
    setup() {
        return { CalendarModal };
    },
});
</script>
