<template>
    <jet-bar-stats-container>
        <dashboard-stat
            v-for="{ title, icon, type, value } in widgets"
            :value="value"
            :icon="icon"
            :type="type"
            :title="title"
        />
    </jet-bar-stats-container>
</template>

<script>
import { defineComponent } from "vue";
import DashboardStat from "@/Pages/Dashboards/Partials/DashboardStat";
import JetBarStatsContainer from "@/Components/JetBarStatsContainer";

export default defineComponent({
    components: {
        DashboardStat,
        JetBarStatsContainer,
    },
    props: {
        widgets: {
            type: Array,
            required: true,
        },
    },
});
</script>
