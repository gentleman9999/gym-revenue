<template>
    <app-layout>
        <template #header> KPI </template>
        <div class="flex flex-row flex-wrap w-full">
            <kpi-revenue-per-member-overall />
            <kpi-membership-revenue-per-member />
            <kpi-personal-training />
            <kpi-retail-revenue-per-member />
            <kpi-membership />
        </div>
        <jet-bar-container> </jet-bar-container>
    </app-layout>
</template>

<style>
.kpi-card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-width: 370px;
}
</style>
<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import JetBarContainer from "@/Components/JetBarContainer";
import KpiRevenuePerMemberOverall from "./components/KpiRevenuePerMemberOverall";
import KpiMembershipRevenuePerMember from "./components/KpiMembershipRevenuePerMember";
import KpiPersonalTraining from "./components/KpiPersonalTraining";
import KpiRetailRevenuePerMember from "./components/KpiRetailRevenuePerMember";
import KpiMembership from "./components/KpiMembership";

export default defineComponent({
    components: {
        AppLayout,
        JetBarContainer,
        KpiRevenuePerMemberOverall,
        KpiMembershipRevenuePerMember,
        KpiPersonalTraining,
        KpiRetailRevenuePerMember,
        KpiMembership,
    },
});
</script>
