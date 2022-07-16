<template>
    <base-comms-action
        :id="id"
        submit-text="Submit"
        :form="form"
        @done="$emit('done')"
    >
        <div v-if="!hideHelpText">
            <p>Contact Via Phone Call</p>
            <p>
                <i>{{ help }}</i>
            </p>
        </div>
        <div class="flex flex-col mb-4">
            <label>Call Outcome</label>
            <select class="form-control" v-model="form.outcome">
                <option
                    v-for="(txt, val) in phoneCallOptions"
                    :value="val"
                    :key="val"
                >
                    {{ txt }}
                </option>
            </select>
        </div>
        <div class="flex flex-col mb-4">
            <label>Call Log/Notes</label>
            <textarea
                class="form-control"
                v-model="form.notes"
                rows="4"
                cols="38"
            ></textarea>
        </div>
        <template #buttons>
            <div class="mr-4">
                <a :href="`tel:${phone}`" class="btn btn-warning">
                    <i class="fad fa-phone-volume"></i> Call Lead
                </a>
            </div>
        </template>
    </base-comms-action>
</template>

<script setup>
import { useGymRevForm } from "@/utils";
import BaseCommsAction from "./BaseCommsAction.vue";
const props = {
    id: {
        type: String,
        required: true,
    },
    phone: {
        type: String,
        required: true,
    },
    hideHelpText: {
        type: Boolean,
        default: false,
    },
};
const phoneCallOptions = {
    "": "Select an Outcome",
    contacted: "Spoke with Lead.",
    voicemail: "Left a Voicemail",
    "hung-up": "Lead Hung Up",
    "wrong-number": "Wrong Number",
    appointment: "An Appointment Was Scheduled",
    sale: "Made the Sale over the Phone!",
};
const form = useGymRevForm({
    method: "phone",
    outcome: null,
    notes: null,
});

const callLead = () => {
    window.open(`tel:${props.phone}`);
};
const emit = defineEmits(["done"]);

const help =
    'Use the text box below to jot down notes during the call with your customer. On your phone, or voice-enabled browser, click "Call Lead" to contact them instantly!';
</script>
