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

<script setup>
import DashboardStat from "@/Pages/Dashboards/Partials/DashboardStat.vue";
import JetBarStatsContainer from "@/Components/JetBarStatsContainer.vue";

const props = defineProps({
    widgets: {
        type: Array,
        required: true,
    },
});
</script>
