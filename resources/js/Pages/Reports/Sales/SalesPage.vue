<template>
    <app-layout>
        <template #header>
            <reports-title>
                <template #title>
                    <h1>Sales</h1>
                </template>
            </reports-title>
        </template>
        <div class="flex flex-row flex-wrap w-full">
            <sales-club></sales-club>
            <div class="flex flex-col w-1/2 md:w-1/4">
                <sales-purchase-date />
                <sales-payment-methods />
            </div>
        </div>
        <jet-bar-container> </jet-bar-container>
    </app-layout>
</template>

<script setup>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import JetBarContainer from "@/Components/JetBarContainer";
import ReportsTitle from "../components/ReportsTitle";
import SalesClub from "./components/SalesClub";
import SalesPurchaseDate from "./components/SalesPurchaseDate";
import SalesPaymentMethods from "./components/SalesPaymentMethods";
</script>
