<template>
    <div class="alt-member" v-if="!selectedMethod">
        <div class="alt-contact-icon" @click="setMethod('sms')">
            <sms-icon />
        </div>
        <div class="alt-contact-icon" @click="setMethod('phone')">
            <phone-icon />
        </div>
    </div>
    <div class="alt-member-details" v-if="selectedMethod">
        <input :placeholder="placeholders[selectedMethod]" v-model="altValue" />
        <div @click="setMethod('')">Close</div>
    </div>
</template>
<style scoped>
.alt-member {
    @apply flex flex-row justify-center space-x-6 mb-8;
}
.alt-member-details {
    @apply flex flex-col items-center mb-7 space-y-1.5;
}
.alt-member-details input {
    @apply text-lg text-center rounded h-10 pl-1 focus:outline-none bg-base-content/20;
    &:focus:focus {
        --tw-ring-shadow: transparent;
    }
}
.alt-contact-icon {
    @apply flex rounded border border-base-content h-14 w-16 items-center justify-center;
}
</style>
<script setup>
import { ref } from "vue";
import { SmsIcon, PhoneIcon } from "@/Components/Icons";

const selectedMethod = ref(null);
const altValue = ref("");

const placeholders = {
    sms: "E-mail",
    phone: "Phone Number",
};

const setMethod = (value) => {
    if (selectedMethod.value !== value) {
        selectedMethod.value = value;
        altValue.value = "";
    }
};
</script>
