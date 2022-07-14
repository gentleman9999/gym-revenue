<template>
    <Head title="Two-factor Confirmation" />

    <jet-authentication-card>
        <template #logo>
            <jet-authentication-card-logo />
        </template>

        <div class="mb-4 text-sm">
            <template v-if="!recovery">
                Please confirm access to your account by entering the
                authentication code provided by your authenticator application.
            </template>

            <template v-else>
                Please confirm access to your account by entering one of your
                emergency recovery codes.
            </template>
        </div>

        <jet-validation-errors class="mb-4" />

        <form @submit.prevent="submit">
            <div v-if="!recovery">
                <jet-label for="code" value="Code" />
                <input
                    ref="code"
                    id="code"
                    type="text"
                    inputmode="numeric"
                    class="mt-1 block w-full"
                    v-model="form.code"
                    autofocus
                    autocomplete="one-time-code"
                />
            </div>

            <div v-else>
                <jet-label for="recovery_code" value="Recovery Code" />
                <input
                    ref="recovery_code"
                    id="recovery_code"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.recovery_code"
                    autocomplete="one-time-code"
                />
            </div>

            <div class="flex items-center justify-end mt-4">
                <button
                    type="button"
                    class="text-sm underline cursor-pointer"
                    @click.prevent="toggleRecovery"
                >
                    <template v-if="!recovery"> Use a recovery code </template>

                    <template v-else> Use an authentication code </template>
                </button>

                <Button
                    class="ml-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
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
import { ref, nextTick } from "vue";
import { Head } from "@inertiajs/inertia-vue3";
import JetAuthenticationCard from "@/Jetstream/AuthenticationCard.vue";
import JetAuthenticationCardLogo from "@/Jetstream/AuthenticationCardLogo.vue";
import Button from "@/Components/Button.vue";

import JetLabel from "@/Jetstream/Label.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
import { useGymRevForm } from "@/utils";

const recovery = ref(false);
const recovery_code = ref(null);
const code = ref(null);
const form = useGymRevForm({
    code: "",
    recovery_code: "",
});
const submit = () => {
    form.post(route("two-factor.login"));
};
const toggleRecovery = () => {
    recovery.value ^= true;

    nextTick(() => {
        if (recovery.value) {
            recovery_code.value.focus();
            form.code = "";
        } else {
            code.value.focus();
            form.recovery_code = "";
        }
    });
};
</script>
