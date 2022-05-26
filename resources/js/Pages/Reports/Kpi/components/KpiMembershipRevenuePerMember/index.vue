<template>
    <kpi-card class="kpi-card" label="Category 2">
        <div class="flex flex-row justify-between items-center mb-3">
            <div class="text-secondary text-lg text-bold">
                Membership Revenue Per Member
            </div>
            <div class="flex flex-row space-x-2 cursor-pointer">
                <span class="flex items-center w-4">
                    <img src="/img/arrow-up.svg" />
                </span>
            </div>
        </div>
        <div class="w-full">
            <membership-chart />
        </div>
        <button-tabs :items="buttons" />
        <div class="flex flex-row pl-6 mt-4 justify-between">
            <div class="space-x-2.5">
                <span class="text-secondary text-base">Total:</span>
                <span class="text-lg text-white">$4,895</span>
            </div>
            <div
                class="flex items-center text-white bg-secondary rounded hover:text-secondary hover:bg-transparent px-2.5 py-1.25"
            >
                Detail
            </div>
        </div>
    </kpi-card>
</template>
<style scoped>
.kpi-card {
    width: 28%;
    min-width: 400px;
}
</style>
<script setup>
import { ref } from "vue";
import { computed } from "@vue/reactivity";
import MembershipChart from "./MembershipChart";
import ButtonTabs from "@/Components/ButtonTabs.vue";
import KpiCard from "@/Components/Card.vue";

const isFavorite = ref(false);
const toggleFavorite = (value) => {
    isFavorite.value = value;
};

const buttons = ["Memberships", "Annual Fees", "Retail Sales"];
</script>
