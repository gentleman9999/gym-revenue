<template>
    <Head title="Email Verification" />

    <jet-authentication-card>
        <template #logo>
            <jet-authentication-card-logo />
        </template>

        <div class="mb-4 text-sm">
            Thanks for signing up! Before getting started, could you verify your
            email address by clicking on the link we just emailed to you? If you
            didn't receive the email, we will gladly send you another.
        </div>

        <div
            class="mb-4 font-medium text-sm text-green-600"
            v-if="verificationLinkSent"
        >
            A new verification link has been sent to the email address you
            provided during registration.
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <Button
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Resend Verification Email
                </Button>

                <inertia-link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="underline text-sm hover:"
                    >Log Out</inertia-link
                >
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
import { computed } from "@vue/reactivity";
import JetAuthenticationCard from "@/Jetstream/AuthenticationCard.vue";
import JetAuthenticationCardLogo from "@/Jetstream/AuthenticationCardLogo.vue";
import Button from "@/Components/Button.vue";
import { Head } from "@inertiajs/inertia-vue3";
import { useGymRevForm } from "@/utils";

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useGymRevForm({});

const submit = () => {
    form.post(route("verification.send"));
};

const verificationLinkSent = computed({
    get() {
        return props.status === "verification-link-sent";
    },
});
</script>
