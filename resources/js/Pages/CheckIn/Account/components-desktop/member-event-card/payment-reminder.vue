<template>
    <div class="payment-reminder-title">Payment Reminder</div>
    <div class="payment-reminder-detail">
        <div class="payment-reminder-due-date">Due Today</div>
        <div class="payment-reminder-price">$45.99</div>
    </div>
    <div class="payment-reminder-action">Send Reminder</div>
</template>
<style scoped>
.payment-reminder-title {
    @apply text-xl font-bold pb-2;
}
.payment-reminder-detail {
    @apply bg-error rounded border border-base-content text-center w-3/5 py-2;
    .payment-reminder-due-date {
        @apply text-xl font-semibold;
    }
    .payment-reminder-price {
        @apply text-base;
    }
}
.payment-reminder-action {
    @apply mt-2 mb-3 text-base font-semibold;
}
</style>
