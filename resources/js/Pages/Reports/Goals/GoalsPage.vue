<template>
    <LayoutHeader title="Goals"> Goals </LayoutHeader>
    <jet-bar-container>
        <h3 class="pb-4">Report Fields</h3>
        <div class="flex flex-col md:flex-row md:space-x-6">
            <div>
                <goals-actual-card
                    :options="{
                        collapse: true,
                        favorite: true,
                    }"
                    class="h-full"
                />
            </div>
            <div>
                <goals-projecting-card
                    :options="{
                        collapse: true,
                        favorite: true,
                    }"
                    mode="normal"
                    class="mb-0"
                />
            </div>
        </div>
        <h3 class="mt-8">Goals</h3>
        <goal-filters class="mb-9" />
        <div class="flex md:flex-row flex-col flex-wrap mx-[-8px]">
            <div
                class="lg:w-1/4 md:w-1/2 px-2 mb-2"
                v-for="goal in goals"
                :key="goal.id"
            >
                <goal-card :data="goal" />
            </div>
        </div>
        <goal-table class="mb-16 data-table" />
        <div class="flex justify-center">
            <Button secondary size="xs">Edit All Goals</Button>
        </div>
    </jet-bar-container>
</template>

<style scoped>
h3 {
    @apply text-secondary font-semibold text-xl;
}
@media only screen and (max-width: 768px) {
    .data-table {
        width: calc(100vw - 32px);
    }
}
</style>

<script setup>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import Button from "@/Components/Button.vue";

import GoalsActualCard from "./components/GoalsActualCard.vue";
import GoalsProjectingCard from "./components/GoalsProjectingCard/GoalsProjectingCard.vue";
import GoalFilters from "./components/GoalFilters.vue";
import GoalCard from "./components/GoalCard.vue";
import GoalTable from "./components/GoalTable.vue";

const goals = [
    {
        id: 1,
        unread: 2,
        title: "NM Unit Goal",
        content: "NM Unit Goal is all in units sold for the month.",
        priority: "high",
        frequency: "repeat",
    },
    {
        id: 2,
        unread: 2,
        title: "NM EFT",
        content:
            "NM EFT amount will reflect all membership sold with the Dollar amount associated with all sales.",
        priority: "high",
        frequency: "repeat",
    },
    {
        id: 3,
        unread: 2,
        title: "Personal Training Units",
        content: "PT Units will reflect all new packages sold for the month.",
        priority: "medium",
        frequency: "repeat",
    },
    {
        id: 4,
        unread: 2,
        title: "PT Total Cash Revenue",
        content:
            "All cash collected pertaining to PT sales: Down payments, Monthly paid installments, Processing fees, and PIFs.",
        priority: "low",
        frequency: "never",
    },
];
</script>
