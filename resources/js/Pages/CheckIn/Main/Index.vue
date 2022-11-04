<template>
    <div class="checkin-dash-container">
        <application-logo class="checkin-logo" />
        <div class="checkin-acc-name">Kevin Buchanan</div>
        <checkin-main-menu @show-modal="showModal" />
        <daisy-modal ref="paymentModal">
            <payment-history />
        </daisy-modal>
        <daisy-modal ref="checkinModal">
            <checkin-history />
        </daisy-modal>
        <daisy-modal ref="workoutModal">
            <workout-history />
        </daisy-modal>
        <daisy-modal ref="inviteModal">
            <invite-friend />
        </daisy-modal>
        <daisy-modal ref="availableClassModal">
            <available-class @show-modal="showModal" />
        </daisy-modal>

        <daisy-modal ref="classSignupModal">
            <class-signup :title="modalProps?.title" />
        </daisy-modal>
    </div>
</template>
<style scoped>
.checkin-dash-container {
    @apply flex flex-col items-center;
    margin-left: -1rem;
    margin-right: -1rem;
}
.checkin-logo {
    @apply h-10 w-auto text-base-content mb-10 mt-4;
}
.checkin-acc-name {
    @apply text-xl font-bold py-4 bg-neutral-content/10 text-center w-full;
}
</style>
<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Jetstream/ApplicationLogo.vue";
import CheckinMainMenu from "./checkin-main-menu.vue";
import DaisyModal from "@/Components/DaisyModal.vue";
import PaymentHistory from "../components/payment-history.vue";
import CheckinHistory from "../components/checkin-history.vue";
import WorkoutHistory from "../components/workout-history.vue";
import InviteFriend from "../components/invite-friend.vue";
import AvailableClass from "../components/available-class.vue";
import ClassSignup from "../components/class-signup.vue";

const paymentModal = ref(null);
const checkinModal = ref(null);
const workoutModal = ref(null);
const inviteModal = ref(null);
const availableClassModal = ref(null);
const classSignupModal = ref(null);

const modalProps = ref(null);
const showModal = (key, data = {}) => {
    console.log("showmodal", key);
    modalProps.value = data;
    switch (key) {
        case "payment":
            paymentModal.value.open();
            break;
        case "checkin":
            checkinModal.value.open();
            break;
        case "workout":
            workoutModal.value.open();
            break;
        case "invite":
            inviteModal.value.open();
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
