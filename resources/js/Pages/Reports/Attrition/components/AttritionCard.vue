<template>
    <Card>
        <div
            class="text-xl mt-[-25px] mx-[-17px] rounded-t pl-6 py-2.5"
            :class="{
                'bg-secondary': type === 'secondary',
                'bg-error': type === 'warning',
                'bg-neutral-500': type === 'gray',
            }"
        >
            {{ title }}
        </div>
        <data-table
            :data="data"
            :columns="columns"
            borderType="secondary"
            :rowBordered="true"
            class="h-72 mt-4"
        />
    </Card>
</template>
<script setup>
import { h } from "vue";
import Card from "@/Components/Card.vue";
import DataTable from "@/Components/DataTable";

const props = defineProps({
    title: {
        type: String,
        default: "Cancellations",
    },
    type: {
        type: String,
        default: "secondary",
    },
    data: {
        type: Array,
        default: [],
    },
});

const columns = [
    {
        field: "avatar",
        label: "",
        noSeparator: true,
        renderer: (value) => h("img", { src: value, class: "m-auto" }),
    },
    {
        field: "name",
        label: "Member",
        renderer: (value) =>
            h("div", { class: "flex h-full items-center" }, value),
        class: "text-left",
    },
    {
        field: "membership",
        label: "Membership Type",
        renderer: (value) =>
            h(
                "div",
                { class: "flex h-full justify-center items-center" },
                value
            ),
    },
    {
        field: "amount",
        label: "Amount ($)",
        renderer: (value) =>
            h(
                "div",
                { class: "flex h-full justify-center items-center" },
                value
            ),
    },
    {
        field: "unit",
        label: "Unit",
        renderer: (value) =>
            h(
                "div",
                { class: "flex h-full justify-center items-center" },
                value
            ),
    },
    {
        field: "",
        label: "",
        renderer: (value) =>
            h("div", { class: "flex h-full items-center justify-center" }, [
                h(
                    "button",
                    { class: "btn w-max rounded btn-secondary btn-xs" },
                    "Contact"
                ),
            ]),
    },
];
</script>
