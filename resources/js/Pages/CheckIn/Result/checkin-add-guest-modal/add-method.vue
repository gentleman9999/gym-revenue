<template>
    <div class="add-method-container" v-if="!selectedMethod">
        <div class="method-icon" @click="setMethod('sms')">
            <sms-icon />
        </div>
        <div class="method-icon" @click="setMethod('phone')">
            <phone-icon />
        </div>
    </div>
    <div class="add-method-details" v-if="selectedMethod">
        <input
            :placeholder="placeholders[selectedMethod]"
            v-model="methodValue"
            class="bg-base-content/20"
        />
        <div @click="setMethod('')">Close</div>
    </div>
</template>
<style scoped>
.add-method-container {
    @apply flex flex-row justify-center space-x-6 mb-8;
}
.add-method-details {
    @apply flex flex-col items-center mb-7 space-y-1.5;
}
.add-method-details input {
    @apply text-lg text-center text-base-content rounded h-10 pl-1 focus:outline-none;
    &:focus:focus {
        --tw-ring-shadow: transparent;
    }
}
.method-icon {
    @apply flex rounded border border-base-content h-14 w-16 items-center justify-center;
}
</style>
<script setup>
import { ref } from "vue";
import { SmsIcon, PhoneIcon } from "@/Components/Icons";

const selectedMethod = ref(null);
const methodValue = ref("");

const placeholders = {
    sms: "E-mail",
    phone: "Phone Number",
};

const setMethod = (value) => {
    if (selectedMethod.value !== value) {
        selectedMethod.value = value;
        methodValue.value = "";
    }
};
</script>
