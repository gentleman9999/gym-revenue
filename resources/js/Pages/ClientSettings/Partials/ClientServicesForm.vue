<template>
    <jet-form-section @submitted="handleSubmit" collapsable>
        <template #title>Services</template>

        <template #description> Enable to disable Client Services.</template>

        <template #form>
            <div
                class="col-span-6 sm:col-span-4 form-control flex-row items-center gap-4"
                v-for="service in availableServices"
            >
                <input
                    :id="service.slug"
                    type="checkbox"
                    v-model="form.services"
                    :value="service.slug"
                />
                <jet-label :for="service.slug" :value="service.feature_name" />
            </div>
            <jet-input-error :message="form.errors.services" class="mt-2" />
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
        availableServices: {
            type: Array,
            default: [],
        },
        services: {
            type: Array,
            default: [],
        },
    },
    setup(props) {
        console.log({
            services: props.services,
            availableServices: props.availableServices,
        });
        const form = useGymRevForm({
            services: props.services.map((detail) => detail.value),
        });
        let handleSubmit = () =>
            form.dirty().post(route("settings.client-services.update"));

        return { form, handleSubmit };
    },
});
</script>
