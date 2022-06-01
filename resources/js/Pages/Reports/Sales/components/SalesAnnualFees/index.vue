<template>
    <sales-card-wrapper class="lg:w-1/4">
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
                        class="border border-secondary rounded outline-none bg-transparent"
                    />
                </div>
                <div>
                    <img src="/img/mocks/sales_page_1.png" />
                </div>
            </div>
        </sales-card>
    </sales-card-wrapper>
</template>
<script setup>
import { ref } from "vue";
import DatePicker from "vue3-date-time-picker";
import SalesCardWrapper from "../SalesCardWrapper";
import SalesCard from "@/Components/Card";
import SelectBox from "@/Components/SelectBox";

const payMethod = ref("");
const setPayMethod = (value) => {
    payMethod.value = value;
};
const payOptions = ["Credit", "Debit", "Cash"];
</script>
