<template>
    <jet-form-section @submitted="handleSubmit" collapsable>
        <template #title>Client Communication Preferences</template>

        <template #description>
            Client default communication preferences.</template
        >

        <template #form>
            <div
                class="col-span-6 sm:col-span-4 form-control flex-row items-center gap-4"
                v-for="{ name, value } in availableCommPreferences"
            >
                <input
                    :id="value"
                    type="checkbox"
                    v-model="form[name.toLowerCase()]"
                    :value="value"
                />
                <jet-label :for="value" :value="name" />
                <jet-input-error
                    :message="form.errors[name.toLowerCase()]"
                    class="mt-2"
                />
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
        commPreferences: {
            type: Object,
            required: true,
        },
        availableCommPreferences: {
            type: Array,
            default: [],
        },
    },
    setup(props) {
        let handleSubmit = () =>
            form.dirty().post(route("settings.client-comms-prefs.update"));

        const form = useGymRevForm({
            email: !!props.commPreferences.email,
            sms: !!props.commPreferences.sms,
        });
        return { form, handleSubmit };
    },
});
</script>
