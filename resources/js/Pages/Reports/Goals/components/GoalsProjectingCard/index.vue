<template>
    <card>
        <template #cardTitle>
            <div class="text-lg text-base-content">Projecting</div>
        </template>
        <div class="flex flex-col">
            <div class="flex flex-wrap text-sm">
                Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed
                diam nonumy eirmod tempor
            </div>
            <div
                class="flex flex-row"
                :class="{
                    'w-full justify-between': mode !== 'mini',
                    'w-fit': mode === 'mini',
                }"
            >
                <div v-if="mode !== 'mini'" class="summary-wrapper">
                    <div class="summary-title">Title</div>
                    <Button
                        secondary
                        size="xs"
                        class="w-52 justify-between"
                        v-for="(title, ndx) in titles"
                        :outline="ndx == 0"
                        :key="ndx"
                    >
                        <running-man-icon />
                        <div class="text-base-content">{{ title.label }}</div>
                        <div class="text-base-content">{{ title.value }}</div>
                    </Button>
                </div>
                <div class="flex flex-col">
                    <projecting-chart :data="data" :height="250" />
                    <div
                        v-if="mode !== 'mini'"
                        class="text-base-content text-sm w-64 m-auto"
                    >
                        Lorem ipsum dolor sit amet, consetetur sadipscing elitr,
                        sed diam.
                    </div>
                </div>
            </div>
        </div>
    </card>
</template>
<style scoped>
.summary-wrapper {
    @apply flex flex-col space-y-3;
}
.summary-title {
    @apply text-base text-base-content font-bold pt-6;
}
</style>
<script setup>
import Card from "@/Components/Card";
import ProjectingChart from "./ProjectingChart";

import Button from "@/Components/Button";
import RunningManIcon from "@/Components/Icons/RunningMan";

const props = defineProps({
    mode: {
        type: String,
        default: "mini",
    },
});

const titles = [
    {
        label: "New Membership Units",
        value: "97",
    },
    {
        label: "NM Dollar Amount",
        value: "$2,716",
    },
    {
        label: "PT Packages Sold",
        value: "22",
    },
    {
        label: "PT Sessions Sold",
        value: "132",
    },
    {
        label: "PT Dollar Amount",
        value: "$3,960",
    },
    {
        label: "Calls",
        value: "654",
    },
];
const data = [82, 70, 75, 40, 25];
</script>
