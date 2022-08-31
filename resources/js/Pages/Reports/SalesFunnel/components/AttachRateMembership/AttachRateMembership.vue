<template>
    <div class="flex flex-col m-auto w-fit mt-16">
        <div class="flex flex-row justify-between">
            <div class="flex flex-col">
                <div class="text-base-content text-bold text-xl">
                    Attachment Rate to Membership
                </div>
                <div class="text-secondary text-base">Subject</div>
            </div>
            <div
                class="flex md:flex-row md:space-x-2 md:space-y-0 flex-col space-y-2"
            >
                <select-box :items="dateList" label="Date" size="xs" />
                <select-box :items="memberList" label="Member" size="xs" />
                <select-box :items="periodList" label="Period" size="xs" />
                <select-box
                    :items="locationList"
                    label="Location(s)"
                    size="xs"
                />
            </div>
        </div>
        <attach-rate-chart />
        <Button size="xs" outline class="self-end">Edit Chart</Button>
    </div>
</template>
<script setup>
import SelectBox from "@/Components/SelectBox";
import AttachRateChart from "./AttachRateChart.vue";
import Button from "@/Components/Button.vue";

const dateList = new Array(4).fill("Date");
const memberList = new Array(4).fill("Member");
const periodList = new Array(4).fill("Period");
const locationList = new Array(4).fill("Location");
</script>
