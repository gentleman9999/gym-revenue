<template>
    <kpi-card-wrapper>
        <kpi-card
            class="kpi-card w-full"
            label="Category 2"
            header="Membership Revenue Per Member"
            :options="{
                collapse: true,
                favorite: false,
            }"
        >
            <div class="w-full">
                <membership-chart />
            </div>
            <div class="card-footer">
                <div class="mt-2">
                    <button-tabs
                        :items="buttons"
                        :activeIndex="activeIndex"
                        :onChange="setActive"
                    />
                </div>
                <div class="flex flex-row mt-4 justify-between">
                    <div class="space-x-2.5">
                        <span class="text-secondary text-base"
                            >Overall Membership:</span
                        >
                        <span class="text-lg text-white">82,348</span>
                    </div>
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
import MembershipChart from "./MembershipChart";
import ButtonTabs from "@/Components/ButtonTabs.vue";
import KpiCard from "@/Components/Card.vue";
import KpiCardWrapper from "../KpiCardWrapper.vue";

const activeIndex = ref(0);
const setActive = (value) => {
    activeIndex.value = value;
};

const buttons = ["Memberships", "Annual Fees", "Retail Sales"];
</script>
