<template>
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 mb-6 sm:pt-0 bg-base-100"
    >
        <div>
            <slot name="logo" />
        </div>

        <div
            class="w-full sm:max-w-md mt-6 mb-9 px-6 py-4 bg-base-300 shadow-md overflow-hidden border border-secondary border-1"
        >
            <slot />
        </div>

        <div
            class="justify-center flex items-center w-full sm:max-w-md pb-6 border-b border-secondary border-1"
            style="margin: 0 auto"
        >
            <inertia-link v-on:click="comingSoon()" class="text-sm hover:">
                Email me a login link
            </inertia-link>
        </div>
        <div
            class="justify-center flex items-center w-full sm:max-w-md pb-4 border-b border-secondary border-1"
            style="margin: 0 auto"
        >
            <inertia-link
                :href="route('password.request')"
                class="mt-4 text-sm hover:"
            >
                Lost your password?
            </inertia-link>
        </div>
    </div>
</template>
<script setup>
import { comingSoon } from "@/utils/comingSoon.js";

import Button from "../Components/Button.vue";

import JetLabel from "./Label.vue";
import JetValidationErrors from "./ValidationErrors.vue";
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
