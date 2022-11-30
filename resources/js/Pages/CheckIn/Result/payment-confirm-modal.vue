<template>
    <card-with-title error title="Confirm Payment" class="bg-base-200">
        <div class="confirm-container">
            <div>Amount of</div>
            <div class="text-3xl mb-3">$45.99</div>
            <div
                class="use-bank"
                :class="{
                    'use-bank-checked': checked,
                    'use-bank-unchecked': !checked,
                }"
                @click="checked = !checked"
            >
                Use Bank or Credit Card Info in Account
            </div>
            <div class="mt-2 mb-6">Add/Update Card Info</div>
            <Button
                error
                class="text-base-content"
                @click="$emit('showModal', 'completed-event', confirmation)"
            >
                Confirm
            </Button>
        </div>
    </card-with-title>
</template>
<style scoped>
.confirm-container {
    @apply flex flex-col items-center pt-4 pb-7 px-2 text-sm;
}
.use-bank {
    @apply rounded px-2 py-1;
}
.use-bank-checked {
    @apply bg-accent text-base-content;
}
.use-bank-unchecked {
    @apply bg-base-content text-base-100;
}
</style>
<script setup>
import { ref } from "vue";
import CardWithTitle from "../components/card-with-title.vue";
import Button from "@/Components/Button.vue";

const checked = ref(false);

const confirmation = {
    title: "Payment Completed",
    msg: "Thank you for your payment!",
};
</script>
