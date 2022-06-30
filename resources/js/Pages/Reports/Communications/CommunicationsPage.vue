<template>
    <LayoutHeader title="Communication">
        <reports-title>
            <template #title> Communication </template>
        </reports-title>
    </LayoutHeader>
    <div class="flex flex-col px-16">
        <div class="text-secondary text-xl font-semibold">Call Reporting</div>
        <div class="flex flex-row space-x-4 my-4">
            <span>Club:</span>
            <select-box :items="locations" label="Locations" size="xs" />
        </div>
        <call-records></call-records>
        <div class="text-secondary text-xl font-semibold mt-9 mb-7">
            Mass Communication
        </div>
        <div class="flex flex-row space-x-4 mb-10 pl-1">
            <Button secondary size="xs">SMS</Button>
            <Button secondary outline size="xs">Email</Button>
        </div>
        <div class="flex flex-col md:px-12">
            <div class="flex flex-row">
                <communication-card
                    title="Deliver Rate"
                    :progress="75"
                    class="md:w-1/4"
                />
                <communication-card
                    title="Open Rate"
                    :progress="60"
                    class="md:w-1/4"
                />
                <communication-card
                    title="Click Rate"
                    :progress="55"
                    class="md:w-1/4"
                />
                <communication-card
                    title="Opt-Out Rate"
                    :progress="25"
                    class="md:w-1/4"
                />
            </div>
            <mass-call-records />
        </div>
    </div>
    <jet-bar-container> </jet-bar-container>
</template>

<script setup>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader";
import JetBarContainer from "@/Components/JetBarContainer";
import ReportsTitle from "../components/ReportsTitle";
import SelectBox from "@/Components/SelectBox";
import Button from "@/Components/Button";
import CallRecords from "./components/CallRecords";
import CommunicationCard from "./components/CommunicationCard";
import MassCallRecords from "./components/MassCallRecords";
const locations = [
    "Location 1",
    "Location 2",
    "Location 3",
    "Location 4",
    "Location 5",
    "Location 6",
];
</script>
