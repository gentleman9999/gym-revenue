<template>
    <div class="col-span-3 items-center">
        <phone-comms-action
            v-if="activeContactMethod === 'phone'"
            :id="id"
            @done="$emit('done')"
            :phone="phone"
            :end-user-type="endUserType"
        />
        <email-comms-action
            v-if="activeContactMethod === 'email'"
            :id="id"
            @done="$emit('done')"
            :end-user-type="endUserType"
        />
        <sms-comms-action
            v-if="activeContactMethod === 'sms'"
            :id="id"
            @done="$emit('done')"
            :end-user-type="endUserType"
        />
    </div>
</template>

<script setup>
import { defineEmits } from "vue";
import EmailCommsAction from "./EmailCommsAction.vue";
import PhoneCommsAction from "./PhoneCommsAction.vue";
import SmsCommsAction from "./SmsCommsAction.vue";
const props = defineProps({
    activeContactMethod: {
        type: String,
        required: true,
    },
    id: {
        type: String,
        required: true,
    },
    phone: {
        type: String,
        required: true,
    },
    endUserType: {
        type: String,
        required: true,
    },
});
const emit = defineEmits(["done"]);
</script>
