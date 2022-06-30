<template>
    <LayoutHeader title="Sales">
        <reports-title>
            <template #title>
                <h1>Sales</h1>
            </template>
        </reports-title>
    </LayoutHeader>
    <div class="flex flex-row flex-wrap w-full">
        <sales-club></sales-club>
        <div class="flex flex-col w-1/2 md:w-1/4">
            <sales-purchase-date />
            <sales-payment-methods />
        </div>
        <sales-annual-fees class="lg:w-1/4" />
    </div>
    <sales-club-table />
    <div class="flex flex-row px-7 w-full">
        <div class="flex flex-col w-2/3 space-y-4 pr-4">
            <div class="flex flex-row flex-wrap">
                <div class="pr-4 w-[53%]"><sales-show-ratio /></div>
                <div class="w-[47%]"><sales-draft-related /></div>
            </div>
            <div class="flex flex-row flex-wrap">
                <div class="w-[47%]"><sales-percent2-goal /></div>
                <div class="w-[53%] pl-4">
                    <sales-conversion-by-entry />
                </div>
            </div>
        </div>
        <div class="w-1/3">
            <sales-employee-break />
            <!-- collapsed -->
        </div>
    </div>
    <div class="m-auto w-fit mt-7">
        <sales-membership />
    </div>
    <jet-bar-container> </jet-bar-container>
</template>

<script setup>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader";
import JetBarContainer from "@/Components/JetBarContainer";
import ReportsTitle from "../components/ReportsTitle";
import SalesClub from "./components/SalesClub";
import SalesPurchaseDate from "./components/SalesPurchaseDate";
import SalesPaymentMethods from "./components/SalesPaymentMethods";
import SalesAnnualFees from "./components/SalesAnnualFees";
import SalesClubTable from "./components/SalesClubTable";
import SalesShowRatio from "./components/SalesShowRatio";
import SalesDraftRelated from "./components/SalesDraftRelated";
import SalesPercent2Goal from "./components/SalesPercent2Goal";
import SalesConversionByEntry from "./components/SalesConversionByEntry";
import SalesEmployeeBreak from "./components/SalesEmployeeBreak";
import SalesMembership from "./components/SalesMembership";
</script>
