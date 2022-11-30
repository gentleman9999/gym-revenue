<template>
    <div class="payment-history-container">
        <div class="mb-5 font-semibold">Payment History</div>
        <div class="text-secondary self-start">Recent Purchases</div>
        <div
            class="payment-history-item"
            v-for="(item, ndx) in mock"
            :key="ndx"
        >
            {{ item }}
        </div>
        <div class="mt-7 font-semibold">Billing Information</div>
        <Button secondary class="mt-8 mb-4">Edit card on file</Button>
    </div>
</template>
<style scoped>
.payment-history-container {
    @apply flex flex-col items-center text-lg;
}
.payment-history-item {
    @apply flex items-center h-9 mt-4 bg-base-content/40 rounded self-start pl-4;
    width: 220px;
}
</style>
<script setup>
import Button from "@/Components/Button.vue";
const mock = ["05/02/2022 3:12 pm", "04/28/2022 5:04 pm", "04/25/2022 1:49 pm"];
</script>
