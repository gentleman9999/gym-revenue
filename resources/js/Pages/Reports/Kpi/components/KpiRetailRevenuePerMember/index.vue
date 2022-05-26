<template>
    <kpi-card-wrapper>
        <kpi-card
            class="kpi-card w-full"
            header="Retail Revenue Per Member"
            :options="{
                collapse: true,
                favorite: true,
            }"
        >
            <div class="w-full">
                <retail-chart />
            </div>
            <div class="card-footer">
                <button-tabs
                    :items="buttons"
                    :activeIndex="activeIndex"
                    :onChange="setActive"
                />

                <div class="flex flex-row pl-6 mt-4 justify-end">
                    <div
                        class="flex items-center text-white bg-secondary rounded hover:text-secondary hover:bg-transparent px-2.5 py-1.25"
                    >
                        Detail
                    </div>
                </div>
            </div>
        </kpi-card>
    </kpi-card-wrapper>
</template>
<script setup>
import { ref } from "vue";
import RetailChart from "./RetailChart";
import ButtonTabs from "@/Components/ButtonTabs.vue";
import KpiCard from "@/Components/Card.vue";
import KpiCardWrapper from "../KpiCardWrapper.vue";

const activeIndex = ref(0);
const setActive = (value) => {
    activeIndex.value = value;
};

const buttons = ["Subject", "Subject", "Subject", "Subject"];
</script>
