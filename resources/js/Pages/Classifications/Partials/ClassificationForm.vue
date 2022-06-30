<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="col-span-6">
                <jet-label for="title" value="Title" />
                <input
                    id="title"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.title"
                    autofocus
                />
                <jet-input-error :message="form.errors.title" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <Button
                type="button"
                @click="$inertia.visit(route('classifications'))"
                :class="{ 'opacity-25': form.processing }"
                error
                outline
                :disabled="form.processing"
            >
                Cancel
            </Button>
            <div class="flex-grow" />
            <Button
                class="btn-secondary"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.isDirty"
                :loading="form.processing"
            >
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import { useGymRevForm } from "@/utils";

export default {
    components: {
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
    },
    props: {
        clientId: {
            type: String,
            required: true,
        },
        classification: {
            type: Object,
        },
    },
    setup(props) {
        let classification = props.classification;
        let operation = "Update";
        if (!classification) {
            classification = {
                title: "",
                id: null,
                client_id: props.clientId,
            };
            operation = "Create";
        }

        const form = useGymRevForm(classification);

        let handleSubmit = () =>
            form
                .dirty()
                .put(route("classifications.update", classification.id));
        if (operation === "Create") {
            handleSubmit = () => form.post(route("classifications.store"));
        }

        return {
            form,
            buttonText: operation,
            handleSubmit,
        };
    },
};
</script>
