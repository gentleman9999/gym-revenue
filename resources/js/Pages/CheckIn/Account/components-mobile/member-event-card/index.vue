<template>
    <check-in-card class="py-4">
        <swiper
            class="w-64"
            :space-between="50"
            :slides-per-view="1"
            :modules="modules"
        >
            <swiper-slide>
                <div class="event-card-content">
                    <payment-reminder @show-modal="showModal" />
                </div>
            </swiper-slide>
            <swiper-slide>
                <div class="event-card-content">
                    <training-reminder />
                </div>
            </swiper-slide>
            <swiper-slide>
                <div class="event-card-content">
                    <class-signup @show-modal="showModal" />
                </div>
            </swiper-slide>
        </swiper>
    </check-in-card>
</template>
<style scoped>
.event-card-content {
    @apply flex flex-col w-full bg-secondary items-center h-40 justify-between py-3;
}
</style>
<script setup>
import { Swiper, SwiperSlide } from "swiper/vue";
import "swiper/css";
import { Pagination } from "swiper";

import CheckInCard from "../../components/check-in-card.vue";
import PaymentReminder from "./payment-reminder.vue";
import TrainingReminder from "./training-reminder.vue";
import ClassSignup from "./class-signup.vue";

const modules = [Pagination];
const props = defineProps({
    showModal: Function,
});
</script>
