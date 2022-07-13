<template>
    <Head title="Reset Password" />

    <jet-authentication-card>
        <template #logo>
            <jet-authentication-card-logo />
        </template>

        <jet-validation-errors class="mb-4" />

        <form @submit.prevent="submit">
            <div>
                <jet-label for="email" value="Email" />
                <input
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                />
            </div>

            <div class="mt-4">
                <jet-label for="password" value="Password" />
                <password-input
                    id="password"
                    class="block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />
            </div>

            <div class="mt-4">
                <jet-label
                    for="password_confirmation"
                    value="Confirm Password"
                />
                <password-input
                    id="password_confirmation"
                    class="block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
            </div>

            <div class="flex items-center justify-end mt-4">
                <Button
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Reset Password
                </Button>
            </div>
        </form>
    </jet-authentication-card>
</template>
<script>
export default {
    layout: null,
};
</script>
<script setup>
import { Head } from "@inertiajs/inertia-vue3";
import JetAuthenticationCard from "@/Jetstream/AuthenticationCard.vue";
import JetAuthenticationCardLogo from "@/Jetstream/AuthenticationCardLogo.vue";
import Button from "@/Components/Button.vue";

import JetLabel from "@/Jetstream/Label.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
import PasswordInput from "@/Components/PasswordInput.vue";
import { useGymRevForm } from "@/utils";

const props = defineProps({
    email: {
        type: Boolean,
    },
    token: {
        type: String,
    },
});

const form = useGymRevForm({
    token: props.token,
    email: props.email,
    password: "",
    password_confirmation: "",
});
const submit = () => {
    form.post(route("password.update"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>
