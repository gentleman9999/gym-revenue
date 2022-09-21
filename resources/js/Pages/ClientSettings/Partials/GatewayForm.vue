<template>
    <jet-form-section @submitted="handleSubmit" collapsable>
        <template #title>Gateway</template>

        <template #description> Fill out Gateways information</template>

        <template #form>
            <div
                class="col-span-6 sm:col-span-6 form-control flex-col lg:flex-row lg:items-center gap-4 border p-2"
            >
                <jet-label for="mailgunDomain" value="Mailgun Domain" />
                <input
                    id="mailgunDomain"
                    type="text"
                    v-model="form.mailgunDomain"
                />
                <jet-label for="mailgunSecret" value="Mailgun Secret" />
                <input
                    id="mailgunSecret"
                    type="text"
                    v-model="form.mailgunSecret"
                />
                <jet-label
                    for="mailgunFromAddress"
                    value="Mailgun From Address"
                />
                <input
                    id="mailgunFromAddress"
                    type="text"
                    v-model="form.mailgunFromAddress"
                />
                <jet-label for="mailgunFromName" value="Mailgun From Name" />
                <input
                    id="mailgunFromName"
                    type="text"
                    v-model="form.mailgunFromName"
                />
            </div>
            <jet-input-error :message="form.errors.email" class="mt-2" />
            <div
                class="col-span-6 sm:col-span-6 form-control flex-col lg:flex-row lg:items-center gap-4 border p-2"
            >
                <jet-label for="twilioSID" value="Twilio SID" />
                <input id="twilioSID" type="text" v-model="form.twilioSID" />
                <jet-label for="twilioToken" value="Twilio Token" />
                <input
                    id="twilioToken"
                    type="text"
                    v-model="form.twilioToken"
                />
                <jet-label for="twilioNumber" value="Twilio Number" />
                <input
                    id="twilioNumber"
                    type="text"
                    v-model="form.twilioNumber"
                />
            </div>
            <jet-input-error :message="form.errors.sms" class="mt-2" />
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>

            <Button
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Save
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import { defineComponent } from "vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import { useGymRevForm } from "@/utils";

export default defineComponent({
    components: {
        JetActionMessage,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
    },
    props: {
        gateways: {
            type: Array,
            default: [],
        },
        availableGateways: {
            type: Array,
            default: [],
        },
    },
    setup(props) {
        let gateways = props.gateways;

        if (!gateways) {
            gateways = {
                mailgunDomain: "",
                mailgunSecret: "",
                mailgunFromAddress: "",
                mailgunFromName: "",
                twilioSID: "",
                twilioToken: "",
                twilioNumber: "",
            };
        } else {
            gateways.mailgunDomain = gateways.find(
                ({ name }) => name === "mailgunDomain"
            )?.value;
            gateways.mailgunSecret = gateways.find(
                ({ name }) => name === "mailgunSecret"
            )?.value;
            gateways.mailgunFromAddress = gateways.find(
                ({ name }) => name === "mailgunFromAddress"
            )?.value;
            gateways.mailgunFromName = gateways.find(
                ({ name }) => name === "mailgunFromName"
            )?.value;
            gateways.twilioSID = gateways.find(
                ({ name }) => name === "twilioSID"
            )?.value;
            gateways.twilioToken = gateways.find(
                ({ name }) => name === "twilioToken"
            )?.value;
            gateways.twilioNumber = gateways.find(
                ({ name }) => name === "twilioNumber"
            )?.value;
        }
        const form = useGymRevForm(gateways);

        let handleSubmit = () => form.put(route("settings.gateway.update"));

        return { form, handleSubmit };
    },
});
</script>
