<template>
    <div class="col-span-3">
        <phone-comms-action
            v-if="activeContactMethod === 'phone'"
            :lead-id="leadId"
            @done="$emit('done')"
            :phone="phone"
        />
        <email-comms-action
            v-if="activeContactMethod === 'email'"
            :lead-id="leadId"
            @done="$emit('done')"
        />
        <sms-comms-action
            v-if="activeContactMethod === 'sms'"
            :lead-id="leadId"
            @done="$emit('done')"
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
    leadId: {
        type: String,
        required: true,
    },
    phone: {
        type: String,
        required: true,
    },
});
const emit = defineEmits(["done"]);
</script>
