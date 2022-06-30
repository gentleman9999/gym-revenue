<template>
    <Card
        :options="{
            borderedTitle: true,
            favorite: true,
            collapse: true,
        }"
        class="flex flex-col h-full pb-0"
    >
        <template #cardTitle>
            <div class="text-base-content text-xl pr-8">CPA, CPL, Clicks</div>
        </template>
        <div class="flex flex-col h-full justify-around">
            <div class="mx-2 border border-base-content rounded px-2">
                <data-table
                    :data="data"
                    :columns="columns"
                    noHeader
                    :borderSeprated="false"
                    class="overflow-hidden"
                />
            </div>
            <div class="flex flex-row justify-end">
                <Button secondary size="xs" class="mt-2">View</Button>
            </div>
        </div>
    </Card>
</template>
<script setup>
import { h } from "vue";
import Card from "@/Components/Card";
import DataTable from "@/Components/DataTable";
import Button from "@/Components/Button";

const columns = [
    {
        field: "type",
        renderer: (value) => h("div", { class: "text-secondary" }, value),
    },
    {
        field: "value_1",
    },
    {
        field: "value_2",
    },
];
const data = [
    {
        type: "CPA",
        value_1: "####",
        value_2: "####",
    },
    {
        type: "CPL",
        value_1: "####",
        value_2: "####",
    },
    {
        type: "Clicks",
        value_1: "####",
        value_2: "####",
    },
];
</script>
