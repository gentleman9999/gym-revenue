<template>
    <div class="mass-com-title">Start your email campaign</div>
    <div class="mass-com-desc">{{ mock }}</div>
    <div class="mass-com-options">
        <div class="option-icon">
            <sms-icon />
        </div>
        <div class="option-icon">
            <chat-icon />
        </div>
        <div
            class="option-icon"
            @click="$emit('show-modal', 'mass-com-phone', { user: mockUser })"
        >
            <phone-icon />
        </div>
        <div class="option-icon">
            <notes-icon />
        </div>
    </div>
    <div class="mass-com-next">
        Start
        <font-awesome-icon :icon="['fas', 'chevron-right']" />
    </div>
</template>
<style setup>
.mass-com-title {
    @apply text-lg font-bold;
}
.mass-com-desc {
    @apply text-sm;
}
.mass-com-options {
    @apply flex flex-row space-x-10 justify-center my-14;
    .option-icon {
        @apply flex rounded border border-base-content h-14 w-16 items-center justify-center cursor-pointer;
    }
}
.mass-com-next {
    @apply text-secondary text-sm font-semibold text-right;
}
</style>
<script setup>
import { computed } from "vue";
import { SmsIcon, ChatIcon, PhoneIcon, NotesIcon } from "@/Components/Icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faChevronRight } from "@fortawesome/pro-solid-svg-icons";
import { library } from "@fortawesome/fontawesome-svg-core";

library.add(faChevronRight);
const mock =
    "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempo";

const mockUser = {
    first_name: "Kevin",
    last_name: "Buchanan",
};
</script>
