<template>
    <base-comms-action
        :id="id"
        submit-text="Submit"
        :form="form"
        :end-user-type="endUserType"
        :allow-submit="isFormValid"
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
            <div class="mr-2 md:mr-4 md:mb-0 mb-3">
                <button
                    @click="initializeCalls"
                    type="button"
                    class="btn btn-warning"
                    :disabled="callStatus !== null"
                >
                    <i class="fad fa-phone-volume"></i>
                    {{ callStatus || `Call ${endUserType}` }}
                </button>
            </div>
        </template>
    </base-comms-action>
</template>

<script setup>
import { useGymRevForm } from "@/utils";
import BaseCommsAction from "./BaseCommsAction.vue";
import { onUnmounted, ref, computed } from "vue";
import { useToast } from "vue-toastification";

const props = defineProps({
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
    endUserType: {
        type: String,
        required: true,
    },
    user: Object,
});

const callStatus = ref(null);
let callWatchInterval = null;

const phoneCallOptions = {
    "": "Select an Outcome",
    CONTACTED: "Spoke with Lead",
    VOICEMAIL: "Left a Voicemail",
    HUNG_UP: "Lead Hung Up",
    WRONG_NUMBER: "Wrong Number",
    APPOINTMENT: "An Appointment Was Scheduled",
    SALE: "Made the Sale over the Phone!",
};
const form = useGymRevForm({
    method: "phone",
    outcome: null,
    notes: null,
    callSid: null,
});

const callLead = () => {
    window.open(`tel:${props.phone}`);
};
const emit = defineEmits(["done"]);

const help =
    'Use the text box below to jot down notes during the call with your customer. On your phone, or voice-enabled browser, click "Call Lead" to contact them instantly!';

// clear ping interval.
onUnmounted(clearCallPingInterval);

async function initializeCalls() {
    // Get toast interface
    const toast = useToast();

    try {
        // initialize call.
        const response = await axios.get(
            route("twilio.call.initialize", [props.phone, props.endUserType])
        );
        callStatus.value = "Call in progress";
        form.callSid = response.data.sid;

        // Ping call status every 2 seconds. we can use ws if this is too expensive.
        callWatchInterval = setInterval(async () => {
            const call = await axios.get(
                route("twilio.call.status", response.data.sid)
            );
            if (call.data.status === "completed") {
                clearCallPingInterval();
            }
        }, 2000);
    } catch (e) {
        toast.error(e.response?.data.message || e.message);
        clearCallPingInterval();
    }
}

function clearCallPingInterval() {
    if (callWatchInterval !== null) {
        callStatus.value = null;
        clearInterval(callWatchInterval);
    }
}

const isFormValid = computed(() => form.outcome?.length && form.notes?.length);
</script>
