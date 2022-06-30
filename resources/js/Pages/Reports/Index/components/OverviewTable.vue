<template>
    <div class="px-20">
        <data-table
            :data="data"
            :columns="columns"
            :rowBordered="true"
            :interactive="true"
            :noHeader="true"
            borderType="secondary"
            class="h-60"
        ></data-table>
    </div>
</template>
<script setup>
import { h } from "vue";
import DataTable from "@/Components/DataTable";
import Button from "@/Components/Button.vue";

const data = [
    {
        label: "Marketing Spend",
        name: "Steve Brightman",
        detail: "Detail",
        service: "Recurring Services",
        info: "Detail Info",
        date: "04.22.22",
    },
    {
        label: "Total New Members",
        name: "Steve Brightman",
        detail: "Detail",
        service: "Recurring Services",
        info: "Detail Info",
        date: "04.22.22",
    },
    {
        label: "Attrition RPM",
        name: "Steve Brightman",
        detail: "Detail",
        service: "Recurring Services",
        info: "Detail Info",
        date: "04.22.22",
    },
    {
        label: "Total Cash Revenue",
        name: "Steve Brightman",
        detail: "Detail",
        service: "Recurring Services",
        info: "Detail Info",
        date: "04.22.22",
    },
    {
        label: "Attrition EFT",
        name: "Steve Brightman",
        detail: "Detail",
        service: "Recurring Services",
        info: "Detail Info",
        date: "04.22.22",
    },
];
const columns = [
    {
        field: "label",
        label: "",
        renderer: (value) => {
            return h("div", { class: "flex flex-row" }, [
                h(
                    "div",
                    { class: "mx-4 w-7 h-7 bg-accent-600 rounded-full" },
                    ""
                ),
                h("div", value),
            ]);
        },
    },
    {
        field: "name",
        label: "",
    },
    {
        field: "detail",
        label: "",
    },
    {
        field: "service",
        label: "",
    },
    {
        field: "info",
        label: "",
    },
    {
        field: "date",
        label: "",
    },
    {
        field: "",
        label: "",
        renderer: (value) => {
            return h(Button, { secondary: true, size: "xs" }, "View");
        },
    },
];
</script>
