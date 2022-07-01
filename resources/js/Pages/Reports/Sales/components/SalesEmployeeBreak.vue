<template>
    <sales-card
        :options="{
            favorite: true,
            collapse: true,
            borderedTitle: true,
        }"
        class="h-full"
    >
        <template #cardTitle>
            <div class="text-base-content text-lg text-bold">
                Employee Breakdown
            </div>
        </template>
        <div class="flex flex-col">
            <data-table
                :collapsed="true"
                :columns="columns"
                :data="data"
                class="h-[500px]"
            ></data-table>
            <Button secondary size="xs" class="self-end mt-6">view</Button>
        </div>
    </sales-card>
</template>
<script setup>
import { h } from "vue";
import SalesCard from "@/Components/Card.vue";
import Button from "@/Components/Button.vue";
import DataTable from "@/Components/DataTable";
const columns = [
    {
        field: "avatar",
        label: "",
        renderer: (value) => {
            return h("img", { src: value });
        },
    },
    {
        field: "name",
        label: "Name",
        class: "text-secondary",
    },
    {
        field: "title",
        label: "Title",
        class: "text-secondary",
    },
    {
        field: "sales",
        label: "Sales ($)",
        class: "text-secondary",
    },
];
const data = [
    {
        name: "Chad Hodges",
        title: "Title",
        sales: "$123.45",
        avatar: "/img/avatars/chad.png",
    },
    {
        name: "Don Torres",
        title: "Title",
        sales: "$123.45",
        avatar: "/img/avatars/don.png",
    },
    {
        name: "Philip Nash",
        title: "Title",
        sales: "$123.45",
        avatar: "/img/avatars/philip.png",
    },
    {
        name: "Stephen Hicks",
        title: "Title",
        sales: "$123.45",
        avatar: "/img/avatars/stephen.png",
    },
    {
        name: "Chad Hodges",
        title: "Title",
        sales: "$123.45",
        avatar: "/img/avatars/chad.png",
    },
    {
        name: "Don Torres",
        title: "Title",
        sales: "$123.45",
        avatar: "/img/avatars/don.png",
    },
    {
        name: "Philip Nash",
        title: "Title",
        sales: "$123.45",
        avatar: "/img/avatars/philip.png",
    },
    {
        name: "Stephen Hicks",
        title: "Title",
        sales: "$123.45",
        avatar: "/img/avatars/stephen.png",
    },
    {
        name: "Chad Hodges",
        title: "Title",
        sales: "$123.45",
        avatar: "/img/avatars/chad.png",
    },
    {
        name: "Don Torres",
        title: "Title",
        sales: "$123.45",
        avatar: "/img/avatars/don.png",
    },
    {
        name: "Philip Nash",
        title: "Title",
        sales: "$123.45",
        avatar: "/img/avatars/philip.png",
    },
    {
        name: "Stephen Hicks",
        title: "Title",
        sales: "$123.45",
        avatar: "/img/avatars/stephen.png",
    },
];
</script>
