<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="col-span-6">
                <jet-label for="name" value="Name" />
                <input
                    id="name"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.name"
                    autofocus
                />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <Button type="button" @click="$inertia.visit(route('users'))" :class="{ 'opacity-25': form.processing }" error outline :disabled="form.processing">
                Cancel
            </Button>
            <div class="flex-grow" />
            <Button class="btn-secondary" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"  :loading="form.processing">
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
        let user = props.user;

        let operation = 'Update';
        if (!user) {
            user = {
                name: null,
                client_id: props.clientId
            }
            operation = 'Create';
        }

        const form = useForm(user);

        let handleSubmit = () => form.put(route('users.update', user.id));
        if (operation === 'Create') {
            handleSubmit = () => form.post(route('users.store'));
        }

        return { form, buttonText: operation, handleSubmit };
    },
};
</script>
