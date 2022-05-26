<template>
    <div class="flex w-2/3 px-3">
        <kpi-card class="kpi-card w-full">
            <div class="flex flex-row justify-between items-end mb-3">
                <div class="text-secondary text-lg text-bold">Membership</div>
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
            <div class="w-full">
                <!-- <retail-chart /> -->
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
import RetailChart from "./RetailChart";
import FavoriteBtn from "@/Components/FavoriteBtn";
import ButtonTabs from "@/Components/ButtonTabs.vue";
import KpiCard from "@/Components/Card.vue";

const isFavorite = ref(false);
const toggleFavorite = (value) => {
    isFavorite.value = value;
};
const activeIndex = ref(0);
const setActive = (value) => {
    activeIndex.value = value;
};

const buttons = ["Subject", "Subject", "Subject", "Subject"];
</script>
