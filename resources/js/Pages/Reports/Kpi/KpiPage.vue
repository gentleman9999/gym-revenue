<template>
    <app-layout>
        <template #header> KPI </template>
        <div class="flex flex-row justify-between">
            <kpi-revenue-per-member-overall />
            <kpi-membership-revenue-per-member />
        </div>
        <jet-bar-container> </jet-bar-container>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import JetBarContainer from "@/Components/JetBarContainer";
import KpiRevenuePerMemberOverall from "./components/KpiRevenuePerMemberOverall";
import KpiMembershipRevenuePerMember from "./components/KpiMembershipRevenuePerMember";

export default defineComponent({
    components: {
        AppLayout,
        JetBarContainer,
        KpiRevenuePerMemberOverall,
        KpiMembershipRevenuePerMember,
    },
});
</script>
