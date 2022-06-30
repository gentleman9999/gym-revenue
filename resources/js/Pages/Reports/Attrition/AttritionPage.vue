<template>
    <LayoutHeader title="Attrition Related">
        <reports-title>
            <template #title> Attrition Related </template>
        </reports-title>
    </LayoutHeader>
    <div class="flex flex-col md:px-28 m-auto">
        <div class="flex flex-row flex-wrap">
            <attrition-running-total />
            <attrition-waterline />
        </div>
        <div class="flex flex-col px-1 space-y-14">
            <attrition-card
                title="Cancellations"
                type="secondary"
                :data="cancellations"
            />
            <attrition-card
                title="Expired Payments"
                type="warning"
                :data="expired"
            />
            <attrition-card
                title="Return for Collections (RFC)"
                type="gray"
                :data="rfc"
            />
        </div>
    </div>
    <jet-bar-container> </jet-bar-container>
</template>

<script setup>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader";
import JetBarContainer from "@/Components/JetBarContainer";
import ReportsTitle from "../components/ReportsTitle";
import AttritionRunningTotal from "./components/AttritionRunningTotal";
import AttritionWaterline from "./components/AttritionWaterline";
import AttritionCard from "./components/AttritionCard";

const cancellations = [
    {
        id: 1,
        amount: "$19.99/ mo",
        avatar: "/img/avatars/KB.png",
        membership: "Premium",
        name: "Kevin Buchanan",
        unit: "Unit",
    },
    {
        id: 2,
        amount: "$9.99/ mo",
        avatar: "/img/avatars/NH.png",
        membership: "Basic",
        name: "Nelle Howard",
        unit: "Unit",
    },
    {
        id: 3,
        amount: "$34.99/ mo",
        avatar: "/img/avatars/RW.png",
        membership: "Premium+",
        name: "Roger Wright",
        unit: "Unit",
    },
    {
        id: 4,
        amount: "$19.99/ mo",
        avatar: "/img/avatars/KB.png",
        membership: "Premium",
        name: "Kevin Buchanan",
        unit: "Unit",
    },
    {
        id: 5,
        amount: "$9.99/ mo",
        avatar: "/img/avatars/NH.png",
        membership: "Basic",
        name: "Nelle Howard",
        unit: "Unit",
    },
    {
        id: 6,
        amount: "$34.99/ mo",
        avatar: "/img/avatars/RW.png",
        membership: "Premium+",
        name: "Roger Wright",
        unit: "Unit",
    },
];
const expired = [
    {
        id: 4,
        amount: "$19.99/ mo",
        avatar: "/img/avatars/GB.png",
        membership: "Premium",
        name: "George Dunnit",
        unit: "Unit",
    },
    {
        id: 5,
        amount: "$9.99/ mo",
        avatar: "/img/avatars/NC.png",
        membership: "Basic",
        name: "Neil Crystal",
        unit: "Unit",
    },
];
const rfc = [
    {
        id: 6,
        amount: "$19.99/ mo",
        avatar: "/img/avatars/MW.png",
        membership: "Premium",
        name: "Martha Wash",
        unit: "Unit",
    },
    {
        id: 7,
        amount: "$9.99/ mo",
        avatar: "/img/avatars/SA.png",
        membership: "Basic",
        name: "Smooth Ash",
        unit: "Unit",
    },
];
</script>
