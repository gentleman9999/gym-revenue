<template>
    <jet-form-section @submitted="updatePassword">
        <template #title> Update Password </template>

        <template #description>
            Ensure your account is using a long, random password to stay secure.
        </template>

        <template #form>
            <div class="col-span-6 sm:col-span-4">
                <jet-label for="current_password" value="Current Password" />
                <password-input
                    id="current_password"
                    class="block w-full"
                    v-model="form.current_password"
                    ref="current_password"
                    autocomplete="current-password"
                />

                <jet-input-error
                    :message="form.errors.current_password"
                    class="mt-2"
                />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="password" value="New Password" />
                <password-input
                    id="password"
                    class="block w-full"
                    v-model="form.password"
                    ref="password"
                    autocomplete="new-password"
                />
                <jet-input-error :message="form.errors.password" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label
                    for="password_confirmation"
                    value="Confirm Password"
                />
                <password-input
                    id="password_confirmation"
                    class="block w-full"
                    v-model="form.password_confirmation"
                    autocomplete="new-password"
                />
                <jet-input-error
                    :message="form.errors.password_confirmation"
                    class="mt-2"
                />
            </div>
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                Saved.
            </jet-action-message>

            <Button
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.isDirty"
            >
                Save
            </Button>
        </template>
    </jet-form-section>
</template>

<script setup>
import { ref } from "vue";
import JetActionMessage from "@/Jetstream/ActionMessage.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import PasswordInput from "@/Components/PasswordInput.vue";
import { useGymRevForm } from "@/utils";

const form = useGymRevForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});
const password = ref(null);
const current_password = ref(null);
function updatePassword() {
    form.put(route("user-password.update"), {
        errorBag: "updatePassword",
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset("password", "password_confirmation");
                password.$refs.input.focus();
            }

            if (form.errors.current_password) {
                form.reset("current_password");
                current_password.$refs.input.focus();
            }
        },
    });
}
</script>
