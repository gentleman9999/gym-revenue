<template>
    <div class="border border-secondary rounded bg-neutral-900 mt-16 p-4">
        <data-table
            :noHeader="true"
            :data="data"
            :columns="columns"
            borderType="secondary"
            :rowBordered="true"
            :borderSeprated="false"
            class="goal-table"
        >
            <template #thead>
                <thead class="text-secondary">
                    <th>Location</th>
                    <th>Goals</th>
                    <th>Goal Amount</th>
                    <th>
                        <div>3 Month Overview Amount</div>
                        <div class="text-base-content text-xs font-normal">
                            <span>Last Month</span>
                            <span>2 Months Prior</span>
                            <span>3 Months Prior</span>
                        </div>
                    </th>
                    <th>
                        <div>3 Month Overview % to Goals</div>
                        <div class="text-base-content text-xs font-normal">
                            <span>Last Month</span>
                            <span>2 Months Prior</span>
                            <span>3 Months Prior</span>
                        </div>
                    </th>
                    <th>Notes</th>
                </thead>
            </template>
        </data-table>
    </div>
</template>
<style>
.goal-table table {
    @apply w-max md:w-full;
}
</style>
<script setup>
import { h } from "vue";
import DataTable from "@/Components/DataTable";
import SelectBox from "@/Components/SelectBox";
import Badge from "@/Components/Badge.vue";

const goalsUnit = [
    "Membership Units",
    "Membership $",
    "Personal Training Units",
    "Personal Training $",
];
const columns = [
    {
        field: "location",
    },
    {
        field: "goal",
        renderer: (value) => {
            return h(SelectBox, {
                size: "xs",
                items: goalsUnit,
                label: value,
                class: "flex h-full items-center m-auto",
            });
        },
    },
    {
        field: "goal_amount",
    },
    {
        field: "overview_amount",
        renderer: (value) => {
            return h("div", { class: "space-x-1 text-lg" }, [
                h(Badge, { label: value[0], type: "error" }),
                h(Badge, { label: value[1], type: "warning" }),
                h(Badge, { label: value[2], type: "accent" }),
            ]);
        },
    },
    {
        field: "overview_to_goals",
        renderer: (value) => {
            return h("div", { class: "space-x-1 text-lg" }, [
                h(Badge, { label: value[0] + "%", type: "neutral" }),
                h(Badge, { label: value[1] + "%", type: "neutral" }),
                h(Badge, { label: value[2] + "%", type: "neutral" }),
            ]);
        },
    },
    {
        field: "notes",
        renderer: (value) => {
            return h(
                "button",
                { class: "bg-base-content text-neutral-500 rounded p-1" },
                "Notes"
            );
        },
    },
];
const data = [
    {
        location: "Location 1",
        goal: "Membership Units",
        goal_amount: "90",
        overview_amount: [72, 93, 100],
        overview_to_goals: [62, 85, 100],
    },
    {
        location: "Location 2",
        goal: "Membership $",
        goal_amount: "$2,800",
        overview_amount: ["$2,500", "$2,500", "$2,500"],
        overview_to_goals: [75, 77, 75],
    },
    {
        location: "Location 3",
        goal: "Personal Training Units",
        goal_amount: "35",
        overview_amount: [31, 40, 32],
        overview_to_goals: [75, 105, 111],
    },
    {
        location: "Location 4",
        goal: "Personal Training $",
        goal_amount: "$4,320",
        overview_amount: ["$4,400", "$4,400", "$4,400"],
        overview_to_goals: [105, 117, 105],
    },
];
</script>
