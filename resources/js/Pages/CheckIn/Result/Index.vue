<template>
    <div class="checkin-result-container">
        <application-logo class="checkin-logo" />
        <summary-card @show-modal="showModal" />
        <div class="w-full mt-5 mb-8">
            <swiper
                class="w-72"
                :slides-per-view="1"
                :space-between="50"
                :modules="modules"
                :pagination="{ clickable: true }"
            >
                <swiper-slide>
                    <upcoming-event
                        eventName="PT Class"
                        @show-modal="showModal"
                    />
                </swiper-slide>
                <swiper-slide>
                    <payment-event amount="45.99" @show-modal="showModal" />
                </swiper-slide>
                <swiper-slide>
                    <upcoming-event
                        eventName="PT Class"
                        @show-modal="showModal"
                    />
                </swiper-slide>
            </swiper>
        </div>
    </div>
    <daisy-modal ref="addGuestModal">
        <checkin-add-guest-modal />
    </daisy-modal>
    <daisy-modal ref="eventModal" class="!bg-neutral-focus/20 !border-none">
        <checkin-event-modal />
    </daisy-modal>
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
</template>
<style scoped>
.checkin-result-container {
    @apply flex flex-col items-center px-3;
}
.checkin-logo {
    @apply h-10 w-auto text-base-content mb-10 mt-4;
}
</style>
<script setup>
import { ref } from "vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import { Pagination } from "swiper";

import ApplicationLogo from "@/Jetstream/ApplicationLogo.vue";
import SummaryCard from "./summary-card.vue";
import UpcomingEvent from "./upcoming-event.vue";
import PaymentEvent from "./payment-event.vue";
import DaisyModal from "@/Components/DaisyModal.vue";
import CheckinAddGuestModal from "./checkin-add-guest-modal/index.vue";
import CheckinEventModal from "./checkin-event-modal/index.vue";
import CheckinCompleteModal from "./checkin-complete-modal.vue";
import PaymentConfirmModal from "./payment-confirm-modal.vue";
import InvoiceModal from "./invoice-modal.vue";

const modules = [Pagination];
const addGuestModal = ref(null);
const eventModal = ref(null);
const completedEventModal = ref(null);
const paymentConfirmModal = ref(null);
const invoiceModal = ref(null);

const modalProps = ref(null);

const showModal = (key, data = {}) => {
    modalProps.value = data;
    switch (key) {
        case "add-guest":
            addGuestModal.value.open();
            break;
        case "event":
            eventModal.value.open();
            break;
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
    }
};
</script>
