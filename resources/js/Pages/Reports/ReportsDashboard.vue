<template>
    <app-layout>
        <template #header> Reporting </template>

        <jet-bar-container>
            <dashboard-stats :widgets="widgets" />

            <div class="container max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <!--                <h1 class="text-secondary font-md text-2xl py-2">Search Reports</h1>-->
                <!-- Table For Reports Goes Here -->

                <h1 class="text-secondary font-md text-2xl py-2">
                    Report Fields
                </h1>
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
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import JetBarContainer from "@/Components/JetBarContainer";
import JetBarAlert from "@/Components/JetBarAlert";
import GymRevenueTable from "@/Components/CRUD/GymRevenueTable";
import JetBarBadge from "@/Components/JetBarBadge";
import JetBarIcon from "@/Components/JetBarIcon";
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
import DashboardStats from "@/Pages/Dashboards/Partials/DashboardStats";
import DashboardHeader from "@/Pages/Dashboards/Partials/DashboardHeader";
import DashboardWidget from "@/Pages/Reports/Partials/DashboardWidget";
import SalesWidget from "@/Pages/Reports/Sales/SalesWidget";
import GoalsWidget from "@/Pages/Reports/Goals/GoalsWidget";
import CalendarWidget from "@/Pages/Reports/Calendar/CalendarWidget";
import AttritionWidget from "@/Pages/Reports/Attrition/AttritionWidget";
import CommunicationsWidget from "@/Pages/Reports/Communications/CommunicationsWidget";
import KpiWidget from "@/Pages/Reports/Kpi/KpiWidget";
import SalesFunnelWidget from "@/Pages/Reports/SalesFunnel/SalesFunnelWidget";
import IndexWidget from "@/Pages/Reports/Index/IndexWidget";

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
        AppLayout,
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
