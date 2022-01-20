<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="col-span-6">
                <jet-label for="name" value="Name" />
                <input
                    id="name"
                    type="text"
                    class="block w-full mt-1"
                    v-model="user.name"
                    autofocus
                />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>
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
import { useForm, usePage } from "@inertiajs/inertia-vue3";

import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";

import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";

export default {
    components: {
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
    },
    props: ["clientId", "user"],
    emits: ['success'],
    setup(props, {emit}) {
        let urlPrev = usePage().props.value.urlPrev;
        let file = props.file;

        const form = useForm(file);

        let handleSubmit = async () => {
            await form.put(route("files.update", file.id));
            emit('success');
        };

        return { form, buttonText: "Update", handleSubmit, urlPrev };
    },
};
</script>
