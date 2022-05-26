<template>
    <div class="flex w-1/3 px-3">
        <kpi-card class="kpi-card w-full" label="Category 3">
            <div class="flex flex-row justify-between items-end">
                <div class="text-secondary text-lg text-bold">
                    Personal Training
                </div>
                <div class="flex flex-row space-x-2 cursor-pointer">
                    <favorite-btn
                        :value="isFavorite"
                        :onChange="toggleFavorite"
                    />
                    <span class="flex items-center w-4">
                        <img src="/img/arrow-up.svg" />
                    </span>
                </div>
            </div>
            <div class="font-bold text-white text-sm mb-3">
                Recurring Services Revenue Per Member
            </div>
            <div class="w-full">
                <training-chart />
            </div>
            <div class="card-footer">
                <div class="flex flex-col mt-4 justify-between">
                    <div class="space-x-2.5">
                        <span class="text-secondary text-base"
                            >Overall PT Units:</span
                        >
                        <span class="text-lg text-white">9,347</span>
                    </div>
                    <div class="space-x-2.5">
                        <span class="text-secondary text-base"
                            >Overall PT $ Amount:</span
                        >
                        <span class="text-lg text-white">$156,098</span>
                    </div>
                </div>
            </div>
        </kpi-card>
    </div>
</template>
<style scoped>
.kpi-card {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-width: 400px;
}
</style>
<script setup>
import { ref } from "vue";
import TrainingChart from "./TrainingChart";
import FavoriteBtn from "@/Components/FavoriteBtn";
import KpiCard from "@/Components/Card.vue";

const isFavorite = ref(false);
const toggleFavorite = (value) => {
    isFavorite.value = value;
};
</script>
