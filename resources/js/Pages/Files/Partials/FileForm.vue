<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="col-span-6">
                <jet-label for="filename" value="Current Filename" />
                <a
                    :href="file.url"
                    :download="file.filename"
                    target="_blank"
                    class="link link-hover"
                    >{{ file.filename }}</a
                >
                <jet-input-error :message="form.errors.file" class="mt-2" />
            </div>

            <div class="col-span-6">
                <jet-label for="filename" value="New Filename" />
                <input
                    id="filename"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.filename"
                    autofocus
                />
                <jet-input-error :message="form.errors.filename" class="mt-2" />
            </div>

            <input id="client_id" type="hidden" v-model="form.client_id" />
            <jet-input-error :message="form.errors.client_id" class="mt-2" />
        </template>

        <template #actions>
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
import { usePage } from "@inertiajs/inertia-vue3";
import { useGymRevForm } from "@/utils";

import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";

export default {
    components: {
        Button,
        JetFormSection,

        JetInputError,
        JetLabel,
    },
    props: ["clientId", "file"],
    emits: ["success"],
    setup(props, { emit }) {
        let urlPrev = usePage().props.value.urlPrev;
        let file = props.file;

        const form = useGymRevForm(file);

        let handleSubmit = async () => {
            await form.dirty().put(route("files.rename", file.id));
            emit("success");
        };

        return { form, buttonText: "Update", handleSubmit, urlPrev };
    },
};
</script>
