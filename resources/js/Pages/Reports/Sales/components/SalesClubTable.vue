<template>
    <sales-card-wrapper class="md:w-full">
        <sales-card class="md:w-full md:px-4 px-4">
            <data-table :columns="columns" :data="data" class="data-table" />
            <div class="flex flex-row space-x-4 justify-end py-6">
                <Button secondary size="xs">Print</Button>
                <Button secondary size="xs">Close</Button>
            </div>
        </sales-card>
    </sales-card-wrapper>
</template>
<style scoped>
@media only screen and (max-width: 768px) {
    .data-table {
        width: calc(100vw - 64px);
    }
}
</style>
<script setup>
import SalesCardWrapper from "./SalesCardWrapper.vue";
import SalesCard from "@/Components/Card.vue";
import Button from "@/Components/Button.vue";
import DataTable from "@/Components/DataTable";

const columns = [
    {
        field: "club",
        label: "Club (1)",
    },
    {
        field: "transaction_cnt",
        label: "Transaction Count",
    },
    {
        field: "quantity",
        label: "Quantity",
    },
    {
        field: "extended_price",
        label: "Extended Price",
    },
    {
        field: "total_discount",
        label: "Total Discounts",
    },
    {
        field: "sub_total",
        label: "Sub Total",
    },
    {
        field: "tax",
        label: "Tax",
    },
    {
        field: "grand_total",
        label: "Grand Total",
    },
];
const data = [
    {
        club: "Location 1",
        transaction_cnt: 4,
        quantity: 13,
        extended_price: "$282.76",
        total_discount: "$8.39",
        sub_total: "$274.37",
        tax: "$2.41",
        grand_total: "$276.78",
    },
    {
        club: "Total",
        transaction_cnt: 4,
        quantity: 13,
        extended_price: "$282.76",
        total_discount: "$8.39",
        sub_total: "$274.37",
        tax: "$2.41",
        grand_total: "$276.78",
    },
];
</script>
