<template>
    <div class="w-full md:w-1/2 px-2">
        <Card
            class="h-80"
            :options="{
                borderedTitle: true,
                favorite: true,
                collapse: true,
            }"
        >
            <template #cardTitle
                ><div class="text-base-content">Running Totals</div></template
            >
            <div class="flex flex-col">
                <select-box :items="items" label="$ Gained" />
                <div class="w-full">
                    <running-total-chart :data="mock" />
                </div>
            </div>
        </Card>
    </div>
</template>
<script setup>
import Card from "@/Components/Card";
import SelectBox from "@/Components/SelectBox";

import RunningTotalChart from "./RunningTotalChart";

const mock = [
    {
        data: [31, 40, 28, 51, 42, 109, 100],
    },
];
const items = ["$ Lost", "Units Gained", "Units Lost"];
</script>
