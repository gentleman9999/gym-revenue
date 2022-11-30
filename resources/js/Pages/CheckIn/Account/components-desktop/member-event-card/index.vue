<template>
    <check-in-card class="py-4">
        <swiper
            class="desktop-swiper"
            :space-between="10"
            :slides-per-view="3"
            :modules="modules"
        >
            <swiper-slide>
                <div class="event-card-content">
                    <payment-reminder />
                </div>
            </swiper-slide>
            <swiper-slide>
                <div class="event-card-content">
                    <training-reminder />
                </div>
            </swiper-slide>
            <swiper-slide>
                <div class="event-card-content">
                    <class-signup />
                </div>
            </swiper-slide>
            <swiper-slide>
                <div class="event-card-content">
                    <training-reminder />
                </div>
            </swiper-slide>
            <swiper-slide>
                <div class="event-card-content">
                    <class-signup />
                </div>
            </swiper-slide>
        </swiper>
    </check-in-card>
</template>
<style scoped>
.event-card-content {
    @apply flex flex-col w-full bg-secondary items-center h-40 justify-start py-3 rounded;
}

.desktop-swiper {
    width: 42vw;
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
</script>
