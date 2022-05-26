<template>
    <kpi-card class="kpi-card" label="Category 1">
        <div class="flex flex-row justify-between items-end mb-3">
            <div class="text-secondary text-lg text-bold">
                Revenue Per Member Overall
            </div>
            <div class="flex flex-row space-x-2 cursor-pointer">
                <favorite-btn :value="isFavorite" :onChange="toggleFavorite" />
                <span class="flex items-center w-4">
                    <img src="/img/arrow-up.svg" />
                </span>
            </div>
        </div>
        <div class="w-full">
            <revenue-chart />
        </div>
        <button-tabs :items="buttons" />
        <div class="flex flex-row pl-6 mt-4 justify-between">
            <div>
                <span class="text-secondary text-base ml-2.5">Total:</span>
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
import RevenueChart from "./RevenueChart";
import FavoriteBtn from "@/Components/FavoriteBtn";
import ButtonTabs from "@/Components/ButtonTabs.vue";
import KpiCard from "@/Components/Card.vue";

const isFavorite = ref(false);
const toggleFavorite = (value) => {
    isFavorite.value = value;
};

const buttons = ["Membership", "Personal Training", "Retail Sales"];
</script>
