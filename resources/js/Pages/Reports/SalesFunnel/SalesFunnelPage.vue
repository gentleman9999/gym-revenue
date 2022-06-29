<template>
    <LayoutHeader title="Sales Funnel">
        <reports-title>
            <template #title>
                <h1>Sales Funnel</h1>
                <div
                    class="flex flex-row text-base-content space-x-2 text-xs items-center"
                >
                    <span class="cursor-pointer text-secondary">Monthly</span>
                    <span class="cursor-pointer">Quartely</span>
                    <span class="cursor-pointer">Annual</span>
                    <span class="cursor-pointer">YOY Comparisons</span>
                </div>
            </template>
        </reports-title>
    </LayoutHeader>
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
        <div>
            <cancel-card progress="25" />
        </div>
        <div>
            <completed-card :progress="[34, 54, 16, 23]" />
        </div>
        <div>
            <lead-stats-card progress="73" />
        </div>
    </div>
    <attach-rate-membership />

    <jet-bar-container> </jet-bar-container>
</template>

<script setup>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader";
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
