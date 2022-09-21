<template>
    <div class="md:px-20 mx-2">
        <data-table
            :data="data"
            :columns="columns"
            :rowBordered="true"
            :interactive="true"
            :noHeader="true"
            borderType="secondary"
            class="overview-table"
        ></data-table>
    </div>
</template>
<style scoped>
.overview-table {
    @apply h-60;
}
@media only screen and (max-width: 768px) {
    .overview-table {
        width: calc(100vw - 50px);
    }
}
</style>

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
            return h("div", { class: "flex flex-row w-full pl-4" }, [
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
