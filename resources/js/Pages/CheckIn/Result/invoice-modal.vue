<template>
    <card-with-title error title="Payment Due Today" class="bg-base-200">
        <div class="invoice-content-container">
            <div class="text-xl font-bold">Kevin Buchanan</div>
            <div>Member Fees Due Today (2022-01-19)</div>
            <div class="text-3xl my-3">$45.99</div>
            <img src="/img/mocks/invoice.png" class="mb-7" />
            <Button
                error
                class="text-base-content"
                @click="$emit('showModal', 'pay-confirm')"
            >
                Pay Now
            </Button>
        </div>
    </card-with-title>
</template>
<style scoped>
.invoice-content-container {
    @apply flex flex-col items-center pt-4 pb-7 px-4 text-sm;
}
</style>
<script setup>
import CardWithTitle from "../components/card-with-title.vue";
import Button from "@/Components/Button.vue";
</script>
