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
                :disabled="form.processing"
                :loading="form.processing"
            >
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import { useGymRevForm } from "@/utils";

export default {
    components: {
        AppLayout,
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
                title: null,
                id: null,
                client_id: props.clientId,
            };
            operation = "Create";
        }

        const form = useGymRevForm(classification);

        let handleSubmit = () =>
            form.put(route("classifications.update", classification.id));
        if (operation === "Create") {
            handleSubmit = () =>
                form.dirty().post(route("classifications.store"));
        }

        return {
            form,
            buttonText: operation,
            handleSubmit,
        };
    },
};
</script>
