<template>
    <jet-form-section @submitted="handleSubmit" collapsable>
        <template #title>Client Communication Preferences</template>

        <template #description>
            Client default communication preferences.</template
        >

        <template #form>
            <div
                class="col-span-6 sm:col-span-4 form-control flex-row items-center gap-4"
                v-for="commpref in availableCommPreferences"
            >
                <input
                    :id="commpref.value"
                    type="checkbox"
                    v-model="form.commPreferences"
                    :value="commpref.value"
                />
                <jet-label :for="commpref.value" :value="commpref.name" />
            </div>
            <jet-input-error
                :message="form.errors.commPreferences"
                class="mt-2"
            />
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
import JetActionMessage from "@/Jetstream/ActionMessage";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";

import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
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
            type: Array,
            default: [],
        },
        availableCommPreferences: {
            type: Array,
            default: [],
        },
    },
    setup(props) {
        let handleSubmit = () =>
            form.dirty().post(route("settings.client-services.update"));

        const form = useGymRevForm({
            commPreferences: props.commPreferences.map(
                (detail) => detail.value
            ),
        });
        return { form, handleSubmit };
    },
});
</script>
