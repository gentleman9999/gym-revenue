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
            <div class="text-white text-lg text-bold">
                Conversion by Entry Source MTD
            </div>
        </template>
        <div class="flex flex-col">
            <img src="/img/mocks/sales_page_5.png" />
            <Button secondary size="xs" class="self-end mt-6">view</Button>
        </div>
    </sales-card>
</template>
<script setup>
import SalesCard from "@/Components/Card";
import Button from "@/Components/Button";
</script>
