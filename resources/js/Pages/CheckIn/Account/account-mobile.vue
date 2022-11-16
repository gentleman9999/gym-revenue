<template>
    <div class="check-in-container">
        <application-logo class="checkin-logo" />
        <member-card />
        <member-event-card :showModal="showModal" />
        <account-form />
    </div>
    <daisy-modal
        ref="completedEventModal"
        class="!bg-neutral-focus/20 !border-none"
    >
        <checkin-complete-modal :data="modalProps" />
    </daisy-modal>
    <daisy-modal
        ref="paymentConfirmModal"
        class="!bg-neutral-focus/20 !border-none"
    >
        <payment-confirm-modal @show-modal="showModal" />
    </daisy-modal>
    <daisy-modal ref="invoiceModal" class="!bg-neutral-focus/20 !border-none">
        <invoice-modal @show-modal="showModal" />
    </daisy-modal>
    <daisy-modal ref="availableClassModal">
        <available-class @show-modal="showModal" />
    </daisy-modal>

    <daisy-modal ref="classSignupModal">
        <class-signup :title="modalProps?.title" />
    </daisy-modal>
</template>
<style scoped>
.check-in-container {
    @apply flex flex-col px-4 space-y-3 pb-20;
}
.checkin-logo {
    @apply h-10 w-auto text-base-content mt-16 mb-10;
}
</style>
<script setup>
import { ref } from "vue";
import DaisyModal from "@/Components/DaisyModal.vue";
import ApplicationLogo from "@/Jetstream/ApplicationLogo.vue";
import MemberCard from "./components-mobile/member-card/index.vue";
import MemberEventCard from "./components-mobile/member-event-card/index.vue";
import AccountForm from "./components-mobile/account-form/index.vue";

import CheckinCompleteModal from "../Result/checkin-complete-modal.vue";
import PaymentConfirmModal from "../Result/payment-confirm-modal.vue";
import InvoiceModal from "../Result/invoice-modal.vue";
import AvailableClass from "../components/available-class.vue";
import ClassSignup from "../components/class-signup.vue";

const completedEventModal = ref(null);
const paymentConfirmModal = ref(null);
const invoiceModal = ref(null);
const availableClassModal = ref(null);
const classSignupModal = ref(null);

const modalProps = ref(null);

const showModal = (key, data = {}) => {
    modalProps.value = data;
    switch (key) {
        case "pay-confirm":
            invoiceModal.value.close();
            paymentConfirmModal.value.open();
            break;

        case "completed-event":
            paymentConfirmModal.value.close();
            completedEventModal.value.open();
            break;
        case "invoice":
            invoiceModal.value.open();
            break;
        case "available-class":
            availableClassModal.value.open();
            break;
        case "class-signup":
            classSignupModal.value.open();
            break;
    }
};
</script>
