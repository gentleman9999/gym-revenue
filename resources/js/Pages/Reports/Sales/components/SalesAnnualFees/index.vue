<template>
    <sales-card-wrapper>
        <sales-card class="w-full">
            <template #cardTitle>
                <div
                    class="text-base-content w-full text-lg text-bold border-b border-neutral-500"
                >
                    Annual Fees
                </div>
            </template>
            <div class="flex flex-col mt-4">
                <select-box
                    label="Method"
                    size="sm"
                    :items="payOptions"
                    :value="payMethod"
                    :onChange="setPayMethod"
                />
                <div class="flex flex-row justify-between my-8">
                    <span class="text-secondary">Amount: </span>
                    <input
                        class="border border-secondary rounded outline-none bg-transparent w-full ml-2"
                    />
                </div>
                <div>
                    <annual-fee-chart :data="splineMock" />
                </div>
            </div>
        </sales-card>
    </sales-card-wrapper>
</template>
<script setup>
import { ref } from "vue";
import DatePicker from "@vuepic/vue-datepicker";
import SalesCardWrapper from "../SalesCardWrapper";
import SalesCard from "@/Components/Card";
import SelectBox from "@/Components/SelectBox";
import AnnualFeeChart from "../Charts/AnnualFeeChart";

const payMethod = ref("");
const setPayMethod = (value) => {
    payMethod.value = value;
};
const payOptions = ["Credit", "Debit", "Cash"];
const splineMock = [
    {
        name: "series1",
        data: [31, 40, 28, 51, 42, 109, 100],
    },
    {
        name: "series2",
        data: [11, 32, 45, 32, 34, 52, 41],
    },
];
</script>
