<template>
    <LayoutHeader title="KPI Dashboard">
        <reports-title>
            <template #title>
                <h1>KPI Dashboard</h1>
                <div
                    class="flex flex-row text-base-content space-x-2 text-xs items-center flex-wrap"
                >
                    <span class="cursor-pointer text-secondary">Monthly</span>
                    <span class="cursor-pointer">Quartely</span>
                    <span class="cursor-pointer">Annual</span>
                    <span class="cursor-pointer">YOY Comparisons</span>
                </div>
            </template>
        </reports-title>
    </LayoutHeader>
    <jet-bar-container>
        <div class="flex flex-row flex-wrap w-full">
            <kpi-revenue-per-member-overall class="no-more md:w-1/2 lg:w-1/3" />
            <kpi-membership-revenue-per-member />
            <kpi-personal-training class="no-more md:w-1/2 lg:w-1/3" />
            <kpi-retail-revenue-per-member />
            <kpi-membership />
            <kpi-other-services />
        </div>
    </jet-bar-container>
</template>

<style>
.kpi-card {
    @apply flex flex-col justify-between md:min-w-[370px];
}
</style>
<script>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import KpiRevenuePerMemberOverall from "./components/KpiRevenuePerMemberOverall/KpiRevenuePerMemberOverall.vue";
import KpiMembershipRevenuePerMember from "./components/KpiMembershipRevenuePerMember/KpiMembershipRevenuePerMember.vue";
import KpiPersonalTraining from "./components/KpiPersonalTraining/KpiPersonalTraining.vue";
import KpiRetailRevenuePerMember from "./components/KpiRetailRevenuePerMember/KpiRetailRevenuePerMember.vue";
import KpiMembership from "./components/KpiMembership.vue";
import KpiOtherServices from "./components/KpiOtherServices/KpiOtherServices.vue";
import ReportsTitle from "../components/ReportsTitle.vue";

export default defineComponent({
    components: {
        LayoutHeader,
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
