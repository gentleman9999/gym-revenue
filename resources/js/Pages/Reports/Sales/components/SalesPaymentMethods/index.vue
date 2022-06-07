<template>
    <sales-card class="w-full">
        <template #cardTitle>
            <div
                class="text-base-content w-full text-lg text-bold border-b border-neutral-500"
            >
                Payment Methods
            </div>
        </template>
        <div class="flex flex-row justify-between items-center mt-6 mb-4">
            <span class="text-secondary text-base">Include:</span>
            <select-box
                label="Method"
                size="sm"
                :items="payOptions"
                :value="payMethod"
                :onChange="setPayMethod"
            />
        </div>
    </sales-card>
</template>
<script setup>
import { ref } from "vue";
import DatePicker from "@vuepic/vue-datepicker";
import SalesCard from "@/Components/Card";
import SelectBox from "@/Components/SelectBox";

const payMethod = ref("");
const setPayMethod = (value) => {
    payMethod.value = value;
};
const payOptions = ["Credit", "Debit", "Cash"];
</script>
