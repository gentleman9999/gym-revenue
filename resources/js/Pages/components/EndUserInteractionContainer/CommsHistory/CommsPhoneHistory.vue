<template>
    <div class="form-control">
        <label class="label">
            <span class="label-text">Outcome</span>
        </label>
        <div class="input input-ghost h-full">
            {{ outcome }}
        </div>
    </div>

    <div class="form-control">
        <label class="label">
            <span class="label-text">Notes</span>
        </label>
        <div class="textarea textarea-ghost h-full">
            {{ detail.misc.notes }}
        </div>
    </div>

    <div class="response">
        <h1 class="">Follow Up</h1>
        <phone-comms-action
            :id="detail.lead_id"
            :phone="detail.phone"
            hide-help-text
            @done="$emit('done')"
            :end-user-type="endUserType"
        />
    </div>
</template>
<style scoped>
.response {
    @apply rounded-lg bg-primary m-4 p-4;
    h1 {
        @apply text-center text-xl font-bold;
    }
}
</style>
<script setup>
import { computed } from "vue";
import PhoneCommsAction from "../CommsActions/PhoneCommsAction.vue";
const props = defineProps({
    detail: {
        type: Object,
        required: true,
    },
    endUserType: {
        type: String,
        default: true,
    },
});

const outcome = computed(() => {
    switch (props.detail.misc?.outcome) {
        case "contacted":
            return "Spoke with Lead.";
        case "voicemail":
            return "Left a Voicemail";
        case "hung-up":
            return "Lead Hung Up";
        case "wrong-number":
            return "Wrong Number";
        case "appointment":
            return "An Appointment Was Scheduled";
        case "sale":
            return "Made the Sale over the Phone!";
    }
});
</script>
