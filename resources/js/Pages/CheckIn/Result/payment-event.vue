<template>
    <card-with-title title="Payment Due Today" error>
        <div class="text-center">
            <div class="payment-event-title">${{ amount }}</div>
            <Button
                error
                class="text-base-content"
                @click="$emit('showModal', 'pay-confirm')"
                >Pay Now</Button
            >
            <div
                class="payment-event-to-detail"
                @click="$emit('showModal', 'invoice')"
            >
                See Invoice
            </div>
        </div>
    </card-with-title>
</template>
<style scoped>
.payment-event-title {
    @apply text-3xl text-base-content mt-4 mb-3;
}
.payment-event-to-detail {
    @apply text-base-content mb-6 mt-3;
}
</style>
<script setup>
import CardWithTitle from "../components/card-with-title.vue";
import Button from "@/Components/Button.vue";

const props = defineProps({
    amount: String,
});
</script>
