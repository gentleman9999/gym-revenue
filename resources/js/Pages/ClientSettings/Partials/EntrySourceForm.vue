<template>
    <jet-form-section @submitted="handleSubmit" collapsable>
        <template #title>Entry Source Preferences</template>

        <template #description>Client default entry source.</template>

        <template #form>
            <div
                class="col-span-6 sm:col-span-4 form-control flex-row items-center gap-4"
                v-for="(
                    { is_default_entry_source, id, name }, index
                ) in entrySources"
            >
                <input
                    :id="id"
                    type="radio"
                    v-model="form.default_entry_source_id"
                    :checked="is_default_entry_source == true"
                    :value="id"
                />
                <jet-label :for="id" role="button" :value="name" />
            </div>
            <jet-input-error
                :message="form.errors.default_entry_source_id"
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
        entrySources: {
            type: Object,
            required: true,
        },
    },
    setup(props) {
        console.log({
            entrySources: props.entrySources,
        });

        const form = useGymRevForm({
            default_entry_source_id: props.entrySources.find(
                ({ is_default_entry_source, id }) => !!is_default_entry_source
            ),
        });
        let handleSubmit = () =>
            form
                .dirty()
                .post(route("settings.client-entry-sources.update", form));

        return { form, handleSubmit };
    },
});
</script>
