<template>
    <base-comms-action
        :disabled="form.processing || !form.isDirty"
        :id="id"
        submit-text="Send Message"
        :form="form"
        :end-user-type="endUserType"
        @done="$emit('done')"
    >
        <div v-if="!hideHelpText">
            <p>Contact Via SMS</p>
            <p>
                <i>{{ help }}</i>
            </p>
        </div>
        <div>
            <sms-form-control name="message" v-model="form.message" />
        </div>
    </base-comms-action>
</template>

<script setup>
import { computed } from "vue";
import { useGymRevForm } from "@/utils";
import BaseCommsAction from "./BaseCommsAction.vue";
import SmsFormControl from "@/Components/SmsFormControl.vue";
const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    charLimit: {
        type: Number,
        default: 130,
    },
    hideHelpText: {
        type: Boolean,
        default: false,
    },
    endUserType: {
        type: String,
        required: true,
    },
});
const form = useGymRevForm({
    method: "sms",
    message: null,
});
const emit = defineEmits(["done"]);

const charsUsed = computed(() => form.message?.length || 0);
const charsLeft = computed(() => props.charLimit - (form.message?.length || 0));
const help =
    "This feature is best utilized to remind them of their upcoming appointment or to send them their enrollment URL.";
</script>
