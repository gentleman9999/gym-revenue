<template>
    <LayoutHeader title="Reporting Dashboard"> Reporting </LayoutHeader>

    <jet-bar-container>
        <dashboard-stats :widgets="widgets" />

        <div class="container max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <!--                <h1 class="text-secondary font-md text-2xl py-2">Search Reports</h1>-->
            <!-- Table For Reports Goes Here -->

            <h1 class="text-secondary font-md text-2xl py-2">Report Fields</h1>
            <div class="grid xl:grid-cols-4 gap-4">
                <sales-widget />
                <goals-widget />
                <calendar-widget />
                <attrition-widget />
                <communications-widget />
                <kpi-widget />
                <sales-funnel-widget />
                <index-widget />
            </div>
        </div>
    </jet-bar-container>
</template>

<script>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import JetBarAlert from "@/Components/JetBarAlert.vue";
import GymRevenueTable from "@/Components/CRUD/GymRevenueTable.vue";
import JetBarBadge from "@/Components/JetBarBadge.vue";
import JetBarIcon from "@/Components/JetBarIcon.vue";
import { Inertia } from "@inertiajs/inertia";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faBars, faCog, faFileUpload } from "@fortawesome/pro-solid-svg-icons";
import {
    faCalendarAlt,
    faChartLine,
    faCommentsAlt,
    faDumbbell,
    faPaste,
    faSatelliteDish,
    faUsers,
    faUser,
} from "@fortawesome/pro-duotone-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import DashboardStats from "@/Pages/Dashboards/Partials/DashboardStats.vue";
import DashboardHeader from "@/Pages/Dashboards/Partials/DashboardHeader.vue";
import DashboardWidget from "@/Pages/Reports/Partials/DashboardWidget.vue";
import SalesWidget from "@/Pages/Reports/Sales/SalesWidget.vue";
import GoalsWidget from "@/Pages/Reports/Goals/GoalsWidget.vue";
import CalendarWidget from "@/Pages/Reports/Calendar/CalendarWidget.vue";
import AttritionWidget from "@/Pages/Reports/Attrition/AttritionWidget.vue";
import CommunicationsWidget from "@/Pages/Reports/Communications/CommunicationsWidget.vue";
import KpiWidget from "@/Pages/Reports/Kpi/KpiWidget.vue";
import SalesFunnelWidget from "@/Pages/Reports/SalesFunnel/SalesFunnelWidget.vue";
import IndexWidget from "@/Pages/Reports/Index/IndexWidget.vue";

library.add(
    faBars,
    faDumbbell,
    faChartLine,
    faCalendarAlt,
    faPaste,
    faSatelliteDish,
    faCommentsAlt,
    faCog,
    faFileUpload,
    faUsers,
    faUser
);

export default {
    components: {
        IndexWidget,
        SalesFunnelWidget,
        KpiWidget,
        CommunicationsWidget,
        AttritionWidget,
        CalendarWidget,
        DashboardWidget,
        LayoutHeader,
        JetBarContainer,
        JetBarAlert,
        GymRevenueTable,
        JetBarBadge,
        JetBarIcon,
        FontAwesomeIcon,
        DashboardStats,
        DashboardHeader,
        SalesWidget,
        GoalsWidget,
    },
};
</script>

<style scoped></style>
