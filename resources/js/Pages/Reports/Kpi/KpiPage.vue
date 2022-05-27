<template>
    <app-layout>
        <template #header>
            <reports-title>
                <template #title>
                    <h1>KPI Dashboard</h1>
                    <div
                        class="flex flex-row text-white space-x-2 text-xs items-center"
                    >
                        <span class="cursor-pointer text-secondary"
                            >Monthly</span
                        >
                        <span class="cursor-pointer">Quartely</span>
                        <span class="cursor-pointer">Annual</span>
                        <span class="cursor-pointer">YOY Comparisons</span>
                    </div>
                </template>
            </reports-title>
        </template>
        <div class="flex flex-row flex-wrap w-full">
            <kpi-revenue-per-member-overall />
            <kpi-membership-revenue-per-member />
            <kpi-personal-training />
            <kpi-retail-revenue-per-member />
            <kpi-membership />
            <kpi-other-services />
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
import KpiOtherServices from "./components/KpiOtherServices";
import ReportsTitle from "../components/ReportsTitle";

export default defineComponent({
    components: {
        AppLayout,
        JetBarContainer,
        KpiRevenuePerMemberOverall,
        KpiMembershipRevenuePerMember,
        KpiPersonalTraining,
        KpiRetailRevenuePerMember,
        KpiMembership,
        KpiOtherServices,
        ReportsTitle,
    },
});
</script>
