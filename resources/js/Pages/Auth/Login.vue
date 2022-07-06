<template>
    <Head title="Log in" />

    <jet-authentication-card>
        <template #logo>
            <jet-authentication-card-logo />
        </template>

        <jet-validation-errors class="mb-4" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <jet-label for="email" value="Email Address or Username" />
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
                <jet-label for="password" value="Password*" />
                <password-input
                    id="password"
                    class="block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        class="checkbox"
                        name="remember"
                        v-model="form.remember"
                    />
                    <span class="ml-2 text-sm">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-center mt-4">
                <!--<inertia-link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm  hover:">
                    Forgot your password?
                </inertia-link>-->

                <Button
                    class="pl-8 pr-8 ml-4 bg-secondary"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Login
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
import { comingSoon } from "@/utils/comingSoon.js";
import JetAuthenticationCard from "@/Jetstream/AuthenticationCard.vue";
import JetAuthenticationCardLogo from "@/Jetstream/AuthenticationCardLogo.vue";
import Button from "@/Components/Button.vue";
import PasswordInput from "@/Components/PasswordInput.vue";

import JetLabel from "@/Jetstream/Label.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
import { Head } from "@inertiajs/inertia-vue3";
import { useGymRevForm } from "@/utils";

const props = defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useGymRevForm({
    email: "",
    password: "",
    remember: false,
});
const submit = () => {
    form.transform((data) => ({
        ...data,
        remember: form.remember ? "on" : "",
    })).post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>
