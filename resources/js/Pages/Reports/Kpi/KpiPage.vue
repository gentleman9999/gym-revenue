<template>
    <app-layout>
        <template #header> KPI </template>
        <kpi-revenue-per-member-overall />
        <jet-bar-container> </jet-bar-container>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import JetBarContainer from "@/Components/JetBarContainer";
import KpiRevenuePerMemberOverall from "./components/KpiRevenuePerMemberOverall";

export default defineComponent({
    components: {
        AppLayout,
        JetBarContainer,
        KpiRevenuePerMemberOverall,
    },
});
</script>
