<template>
    <LayoutHeader title="Sales">
        <reports-title>
            <template #title>
                <h1>Sales</h1>
            </template>
        </reports-title>
    </LayoutHeader>
    <jet-bar-container>
        <div class="flex flex-row flex-wrap w-full">
            <sales-club class="w-full lg:w-1/2"></sales-club>
            <div class="flex flex-col w-full md:w-1/2 lg:w-1/4 justify-between">
                <sales-purchase-date />
                <sales-payment-methods />
            </div>
            <sales-annual-fees class="lg:w-1/4 md:w-1/2" />
        </div>
        <sales-club-table />
        <div
            class="flex lg:flex-row space-y-4 lg:space-y-0 flex-col md:px-3 w-full"
        >
            <div class="flex flex-col lg:w-2/3 w-full space-y-4 lg:pr-4">
                <div class="flex md:flex-row flex-col flex-wrap">
                    <div class="md:pr-4 md:w-[53%]"><sales-show-ratio /></div>
                    <div class="md:w-[47%]"><sales-draft-related /></div>
                </div>
                <div class="flex md:flex-row flex-col flex-wrap">
                    <div class="md:w-[47%]"><sales-percent2-goal /></div>
                    <div class="md:w-[53%] md:pl-4">
                        <sales-conversion-by-entry />
                    </div>
                </div>
            </div>
            <div class="lg:w-1/3">
                <sales-employee-break />
            </div>
        </div>
        <div class="m-auto w-fit mt-7">
            <sales-membership />
        </div>
    </jet-bar-container>
</template>

<script setup>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import ReportsTitle from "../components/ReportsTitle.vue";
import SalesClub from "./components/SalesClub/SalesClub.vue";
import SalesPurchaseDate from "./components/SalesPurchaseDate.vue";
import SalesPaymentMethods from "./components/SalesPaymentMethods.vue";
import SalesAnnualFees from "./components/SalesAnnualFees/SalesAnnualFees.vue";
import SalesClubTable from "./components/SalesClubTable.vue";
import SalesShowRatio from "./components/SalesShowRatio/SalesShowRatio.vue";
import SalesDraftRelated from "./components/SalesDraftRelated/SalesDraftRelated.vue";
import SalesPercent2Goal from "./components/SalesPercent2Go/SalesPercent2Go.vue";
import SalesConversionByEntry from "./components/SalesConversionByEntry/SalesConversionByEntry.vue";
import SalesEmployeeBreak from "./components/SalesEmployeeBreak.vue";
import SalesMembership from "./components/SalesMembership/SalesMembership.vue";
</script>
