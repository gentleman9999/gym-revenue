<template>
    <app-layout>
        <template #header>
            <reports-title>
                <template #title>
                    <h1>Sales Funnel</h1>
                    <div
                        class="flex flex-row text-base-content space-x-2 text-xs items-center"
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
        <div class="flex flex-row mt-[-24px] px-20">
            <div class="w-[37.5%]">
                <status-cards-wrapper
                    title="Actual"
                    :cards="actual_status"
                ></status-cards-wrapper>
            </div>
            <div class="w-[62.5%]">
                <status-cards-wrapper title="Active" :cards="active_status">
                    <template #filter>
                        <status-filter />
                    </template>
                </status-cards-wrapper>
            </div>
        </div>

        <div class="flex flex-row px-20 pt-6 justify-between">
            <div class="pl-2">
                <cancel-card progress="/img/mocks/clalendar_page_1.png" />
            </div>
            <div>
                <completed-card progress="/img/mocks/clalendar_page_2.png" />
            </div>
            <div class="pr-2">
                <lead-stats-card progress="/img/mocks/clalendar_page_3.png" />
            </div>
        </div>
        <div>
            <attach-rate-membership />
        </div>

        <jet-bar-container> </jet-bar-container>
    </app-layout>
</template>

<script setup>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import JetBarContainer from "@/Components/JetBarContainer";
import ReportsTitle from "../components/ReportsTitle";
import StatusCardsWrapper from "./components/StatusCardsWrapper";
import StatusFilter from "./components/StatusFilter";
import CancelCard from "./components/CancelCard";
import CompletedCard from "./components/CompletedCard";
import LeadStatsCard from "./components/LeadStatsCard";
import AttachRateMembership from "./components/AttachRateMembership";
const actual_status = [
    { status: "leads", count: 55 },
    { status: "booked", count: 23 },
    { status: "confirmed", count: 14 },
];

const active_status = [
    { status: "referrals", count: 37 },
    { status: "contacts", count: 108 },
    { status: "active_booked", count: 33 },
    { status: "sales", count: 42 },
    { status: "rejections", count: 178 },
];
</script>
