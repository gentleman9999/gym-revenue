<template>
    <card class="text-lg font-semibold">
        <div>Recent Events</div>
        <data-table
            :data="mock"
            :columns="columns"
            borderType="secondary"
            :rowBordered="true"
            class="recent-events-table"
        />
    </card>
</template>
<style scoped>
.recent-events-table {
    @apply h-72 overflow-y-scroll;
}
</style>
<script setup>
import { h } from "vue";
import DataTable from "@/Components/DataTable";
import Card from "@/Components/Card.vue";
import { getAvatarImg } from "@/utils";

const columns = [
    {
        field: "name",
        label: "",
        noSeparator: true,
        renderer: (value) =>
            h("img", {
                src: getAvatarImg(...value.split(" ")),
                class: "rounded-full m-auto w-14",
            }),
    },
    {
        field: "name",
        label: "Trainer",
        renderer: (value) =>
            h(
                "div",
                { class: "flex w-full h-full justify-start items-center" },
                value
            ),
        class: "text-left",
    },
    {
        field: "class",
        label: "Class",
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

const mock = [
    {
        id: 1,
        class: "Cardio",
        name: "Kevin Buchanan",
    },
    {
        id: 2,
        class: "Basic",
        name: "Nelle Howard",
    },
    {
        id: 3,
        class: "Premium+",
        name: "Roger Wright",
    },
    {
        id: 4,
        class: "Premium",
        name: "Kevin Buchanan",
    },
    {
        id: 5,
        class: "Basic",
        name: "Nelle Howard",
    },
    {
        id: 6,
        class: "Premium+",
        name: "Roger Wright",
    },
];
</script>
