<template>
    <account-title @show-modal="showModal" />
    <div class="flex flex-row px-40 mt-6">
        <div class="flex flex-col w-2/3 px-4 space-y-4">
            <member-card />
            <member-event-card />
            <recent-event />
            <mass-notes />
        </div>
        <div class="w-1/3 px-4 space-y-4">
            <member-calendar />
            <comm-history />
            <transaction-log />
        </div>
    </div>
    <daisy-modal ref="guestPassModal">
        <guest-pass-modal @close="handleClose" @show-modal="showModal" />
    </daisy-modal>
    <daisy-modal ref="guestPassPreviewModal">
        <guest-pass-preview />
    </daisy-modal>
    <daisy-modal ref="massComModal">
        <mass-com-modal @show-modal="showModal" />
    </daisy-modal>
    <daisy-modal ref="massComPhone">
        <mass-com-phone @show-modal="showModal" :data="modalProps" />
    </daisy-modal>
</template>
<style scoped></style>
<script setup>
import { ref } from "vue";
import DaisyModal from "@/Components/DaisyModal.vue";
import Button from "@/Components/Button.vue";
import AccountTitle from "./components-desktop/account-title.vue";
import GuestPassModal from "./components-desktop/guest-pass-modal/index.vue";
import GuestPassPreview from "./components-desktop/guest-pass-preview/index.vue";
import MassComModal from "./components-desktop/mass-com-modal/index.vue";
import MassComPhone from "./components-desktop/mass-com-phone/index.vue";
import MemberCard from "./components-desktop/member-card/index.vue";
import MemberEventCard from "./components-desktop/member-event-card/index.vue";
import RecentEvent from "./components-desktop/recent-event/index.vue";
import MassNotes from "./components-desktop/mass-notes/index.vue";
import MemberCalendar from "./components-desktop/member-calendar.vue";
import CommHistory from "./components-desktop/comm-history/index.vue";
import TransactionLog from "./components-desktop/transaction-log/index.vue";

const guestPassModal = ref(null);
const guestPassPreviewModal = ref(null);
const massComModal = ref(null);
const massComPhone = ref(null);
const modalProps = ref(null);

const dict = {
    "guest-pass": guestPassModal,
    "guest-pass-preview": guestPassPreviewModal,
    "mass-com": massComModal,
    "mass-com-phone": massComPhone,
};

const showModal = (key, data) => {
    if (key === "mass-com-phone") {
        massComPhone.value.mode = "contact";
    }
    modalProps.value = data;
    dict[key].value.open();
};

const handleClose = (key) => {
    console.log("handle");
    dict[key].value.close();
};
</script>
