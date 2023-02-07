<template>
    <jet-form-section @submitted="handleSubmit" collapsable>
        <template #title>Client Communication Preferences</template>

        <template #description
            >Client default communication preferences.</template
        >

        <template #form>
            <div
                class="col-span-6 sm:col-span-4 form-control flex-row items-center gap-4"
                v-for="(name, value) in availableCommPreferences"
            >
                <input
                    :id="value"
                    type="checkbox"
                    :disabled="preventSelection(value)"
                    v-model="form[value]"
                    :value="value"
                />
                <jet-label :for="value" role="button" :value="name" />
                <jet-input-error :message="form.errors[value]" class="mt-2" />
            </div>
            <div
                class="text-xs text-warning col-span-6 sm:col-span-4 flex-row"
                style="display: ruby"
            >
                <template v-if="hasTwilioSetup">
                    Conversation and Text are a bit similar, the difference is
                    that conversation allows a continues interaction between you
                    and the lead/member, while text is a one way event.
                </template>
                <template v-else>
                    SMS and Conversation requires twilio gateway data (
                    <b>Twilio SID</b>, <b>Twilio Token</b> and
                    <b>Twilio Number</b>) to work.
                </template>
            </div>
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>

            <Button
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.isDirty"
            >
                Save
            </Button>
        </template>
    </jet-form-section>
</template>

<script setup>
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import {
    COMMUNICATION_TYPES_CONVERSATION,
    COMMUNICATION_TYPES_SMS,
    COMMUNICATION_TYPES_VOICE,
    COMMUNICATION_TYPES_EMAIL,
} from "../Constants/ClientCommunicationPreference.const";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import { useGymRevForm } from "@/utils";
import { computed } from "@vue/reactivity";

const props = defineProps({
    commPreferences: {
        type: Object,
        required: true,
    },
    availableCommPreferences: {
        type: Object,
    },
    gateways: {
        type: Array,
        default: [],
    },
});

const hasTwilioSetup = computed(() => {
    const gatewayNames = props.gateways.map((g) => g.name);

    return ["twilioSID", "twilioToken", "twilioNumber"].every((name) =>
        gatewayNames.includes(name)
    );
});

console.log(222, hasTwilioSetup.value);

const form = useGymRevForm({
    [COMMUNICATION_TYPES_CONVERSATION]:
        props.commPreferences[COMMUNICATION_TYPES_CONVERSATION],
    [COMMUNICATION_TYPES_SMS]: props.commPreferences[COMMUNICATION_TYPES_SMS],
    [COMMUNICATION_TYPES_VOICE]:
        props.commPreferences[COMMUNICATION_TYPES_VOICE],
    [COMMUNICATION_TYPES_EMAIL]:
        props.commPreferences[COMMUNICATION_TYPES_EMAIL],
});

function handleSubmit() {
    form.post(route("settings.twilio-comms-prefs.update"));
    // "settings.client-comms-prefs.update"));
}

function preventSelection(value) {
    return (
        (value === COMMUNICATION_TYPES_CONVERSATION &&
            (!hasTwilioSetup.value || form[COMMUNICATION_TYPES_SMS])) ||
        (value === COMMUNICATION_TYPES_SMS &&
            (!hasTwilioSetup.value || form[COMMUNICATION_TYPES_CONVERSATION]))
    );
}
</script>
