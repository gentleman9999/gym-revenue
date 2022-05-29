<template>
    <sales-card
        :options="{
            favorite: true,
            collapse: true,
            borderedTitle: true,
        }"
        class="h-full"
    >
        <template #cardTitle>
            <div class="text-white text-lg text-bold">Show Ratio</div>
        </template>
        <div class="flex flex-col">
            <img src="/img/mocks/sales_page_2.png" />
            <div class="text-center mt-3 mb-7">First Appointment Status</div>
            <Button secondary size="xs" class="self-end">view</Button>
        </div>
    </sales-card>
</template>
<script setup>
import SalesCard from "@/Components/Card";
import Button from "@/Components/Button";
</script>
