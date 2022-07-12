<template>
    <jet-action-section>
        <template #title> Two Factor Authentication </template>

        <template #description>
            Add additional security to your account using two factor
            authentication.
        </template>

        <template #content>
            <h3 class="text-lg font-medium" v-if="twoFactorEnabled">
                You have enabled two factor authentication.
            </h3>

            <h3 class="text-lg font-medium" v-else>
                You have not enabled two factor authentication.
            </h3>

            <div class="mt-3 max-w-xl text-sm">
                <p>
                    When two factor authentication is enabled, you will be
                    prompted for a secure, random token during authentication.
                    You may retrieve this token from your phone's Google
                    Authenticator application.
                </p>
            </div>

            <div v-if="twoFactorEnabled">
                <div v-if="qrCode">
                    <div class="mt-4 max-w-xl text-sm">
                        <p class="font-semibold">
                            Two factor authentication is now enabled. Scan the
                            following QR code using your phone's authenticator
                            application.
                        </p>
                    </div>

                    <div class="mt-4" v-html="qrCode"></div>
                </div>

                <div v-if="recoveryCodes.length > 0">
                    <div class="mt-4 max-w-xl text-sm">
                        <p class="font-semibold">
                            Store these recovery codes in a secure password
                            manager. They can be used to recover access to your
                            account if your two factor authentication device is
                            lost.
                        </p>
                    </div>

                    <div
                        class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-mono text-sm bg-base-100 rounded-lg"
                    >
                        <div v-for="code in recoveryCodes" :key="code">
                            {{ code }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <div v-if="!twoFactorEnabled">
                    <jet-confirms-password
                        @confirmed="enableTwoFactorAuthentication"
                    >
                        <Button
                            type="button"
                            :class="{ 'opacity-25': enabling }"
                            :disabled="enabling"
                        >
                            Enable
                        </Button>
                    </jet-confirms-password>
                </div>

                <div v-else>
                    <jet-confirms-password @confirmed="regenerateRecoveryCodes">
                        <jet-secondary-button
                            class="mr-3"
                            v-if="recoveryCodes.length > 0"
                        >
                            Regenerate Recovery Codes
                        </jet-secondary-button>
                    </jet-confirms-password>

                    <jet-confirms-password @confirmed="showRecoveryCodes">
                        <jet-secondary-button
                            class="mr-3"
                            v-if="recoveryCodes.length === 0"
                        >
                            Show Recovery Codes
                        </jet-secondary-button>
                    </jet-confirms-password>

                    <jet-confirms-password
                        @confirmed="disableTwoFactorAuthentication"
                    >
                        <button
                            class="error"
                            :class="{ 'opacity-25': disabling }"
                            :disabled="disabling"
                        >
                            Disable
                        </button>
                    </jet-confirms-password>
                </div>
            </div>
        </template>
    </jet-action-section>
</template>

<script setup>
import { ref } from "vue";
import { computed } from "@vue/reactivity";
import JetActionSection from "@/Jetstream/ProfileActionSection.vue";
import Button from "@/Components/Button.vue";
import JetConfirmsPassword from "@/Jetstream/ConfirmsPassword.vue";

import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage } from "@inertiajs/inertia-vue3";

const enabling = ref(false);
const disabling = ref(false);
const page = usePage();

const qrCode = ref(null);
const recoveryCodes = ref([]);
function enableTwoFactorAuthentication() {
    enabling.value = true;

    Inertia.post(
        "/user/two-factor-authentication",
        {},
        {
            preserveScroll: true,
            onSuccess: () => Promise.all([showQrCode(), showRecoveryCodes()]),
            onFinish: () => (enabling.value = false),
        }
    );
}

function showQrCode() {
    return axios.get("/user/two-factor-qr-code").then((response) => {
        qrCode.value = response.data.svg;
    });
}

function showRecoveryCodes() {
    return axios.get("/user/two-factor-recovery-codes").then((response) => {
        recoveryCodes.value = response.data;
    });
}

function regenerateRecoveryCodes() {
    axios.post("/user/two-factor-recovery-codes").then((response) => {
        showRecoveryCodes();
    });
}

function disableTwoFactorAuthentication() {
    disabling.value = true;

    Inertia.delete("/user/two-factor-authentication", {
        preserveScroll: true,
        onSuccess: () => (disabling.value = false),
    });
}

const twoFactorEnabled = computed({
    get() {
        return !enabling.value && page.props.value.user.two_factor_enabled;
    },
});
</script>
