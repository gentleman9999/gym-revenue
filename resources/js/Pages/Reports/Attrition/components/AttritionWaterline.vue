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
                ><div class="text-base-content">Waterline</div></template
            >
            <div class="flex flex-col">
                <div class="w-full">
                    <waterline-chart :data="mock" />
                </div>
            </div>
        </Card>
    </div>
</template>
<script setup>
import Card from "@/Components/Card";
import SelectBox from "@/Components/SelectBox";
import RectangleIcon from "@/Components/Icons/Rectangle";
import WaterlineChart from "./Charts/WaterlineChart";

const items = ["$ Lost", "Units Gained", "Units Lost"];

const mock = [
    {
        name: "New Members",
        data: [31, 40, 28, 51, 42, 109, 100],
    },
    {
        name: "Renewals",
        data: [11, 32, 45, 32, 34, 52, 41],
    },
];
</script>
