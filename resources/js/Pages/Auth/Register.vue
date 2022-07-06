<template>
    <Head title="Register" />

    <jet-authentication-card>
        <template #logo>
            <jet-authentication-card-logo />
        </template>

        <jet-validation-errors class="mb-4" />
        <form @submit.prevent="submit" v-if="showRegistration">
            <p class="text-center">
                Complete the registration to activate your account for
            </p>
            <div v-if="extraImg !== ''" class="text-center">
                <br />
                <p><img :src="extraImg" style="display: unset" /></p>
                <br />
            </div>
            <p v-if="extraImg === ''" class="text-center">
                <b>{{ client }}</b>
            </p>
            <br />
            <p>
                Role - <b>{{ role }}</b>
            </p>
            <p>
                Team - <b>{{ team }}</b>
            </p>
            <br />

            <div>
                <jet-label for="name" value="Name" />
                <input
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
            </div>

            <div class="mt-4">
                <jet-label for="email" value="Email" />
                <input
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
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

            <div
                class="mt-4"
                v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature"
            >
                <jet-label for="terms">
                    <div class="flex items-center">
                        <input
                            type="checkbox"
                            class="checkbox"
                            name="terms"
                            id="terms"
                            v-model="form.terms"
                        />

                        <div class="ml-2">
                            Check the box and don't tell people about our shit!
                            <br />
                            You can look at the
                            <a
                                target="_blank"
                                :href="route('terms.show')"
                                class="underline text-sm hover:"
                                >Terms of Service</a
                            >
                            and
                            <a
                                target="_blank"
                                :href="route('policy.show')"
                                class="underline text-sm hover:"
                                >Privacy Policy</a
                            >
                            too.
                        </div>
                    </div>
                </jet-label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <inertia-link
                    :href="route('login')"
                    class="underline text-sm hover:"
                >
                    Already registered?
                </inertia-link>

                <Button
                    class="ml-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </Button>
            </div>
        </form>
        <form v-else>
            <p class="text-center">{{ errorMsg }}</p>
        </form>
    </jet-authentication-card>
</template>
<script>
export default {
    layout: null,
};
</script>
<script setup>
import JetAuthenticationCard from "@/Jetstream/AuthenticationCard.vue";
import JetAuthenticationCardLogo from "@/Jetstream/AuthenticationCardLogo.vue";
import Button from "@/Components/Button.vue";

import JetLabel from "@/Jetstream/Label.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors.vue";
import PasswordInput from "@/Components/PasswordInput.vue";
import { useGymRevForm } from "@/utils";

const props = defineProps({
    showRegistration: {
        type: Boolean,
        default: false,
    },
    errorMsg: {
        type: String,
        default: "",
    },
    role: {
        type: String,
        default: "",
    },
    team: {
        type: String,
        default: "",
    },
    teamId: {
        type: String,
        default: "",
    },
    client: {
        type: String,
        default: "",
    },
    clientId: {
        type: String,
        default: "",
    },
    extraImg: {
        type: String,
        default: "",
    },
});
const form = useGymRevForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    role: props.role,
    team: props.team,
    client: props.client,
    terms: false,
});
const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>
