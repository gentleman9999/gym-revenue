<template>
    <div class="flex flex-row">
        <div class="flex flex-col">
            <div class="text-base-content text-bold text-xl">
                Attachment Rate to Membership
            </div>
            <div class="text-secondary text-base">Subject</div>
        </div>
        <div class="flex flex-row">
            <select-box :items="dateList" label="Date" size="xs" />
            <select-box :items="memberList" label="Member" size="xs" />
            <select-box :items="periodList" label="Period" size="xs" />
            <select-box :items="locationList" label="Location(s)" size="xs" />
        </div>
    </div>
</template>
<script setup>
import SelectBox from "@/Components/SelectBox";
const dateList = new Array(4).fill("Date");
const memberList = new Array(4).fill("Member");
const periodList = new Array(4).fill("Period");
const locationList = new Array(4).fill("Location");
</script>
