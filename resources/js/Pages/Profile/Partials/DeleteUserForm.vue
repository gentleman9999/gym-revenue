<template>
    <jet-action-section>
        <template #title> Delete Account </template>

        <template #description> Permanently delete your account. </template>

        <template #content>
            <div class="max-w-xl text-sm">
                Once your account is deleted, all of its resources and data will
                be permanently deleted. Before deleting your account, please
                download any data or information that you wish to retain.
            </div>

            <div class="mt-5">
                <button class="error" @click="confirmUserDeletion">
                    Delete Account
                </button>
            </div>

            <!-- Delete Account Confirmation Modal -->
            <jet-dialog-modal
                :show="confirmingUserDeletion"
                @close="closeModal"
            >
                <template #title> Delete Account </template>

                <template #content>
                    Are you sure you want to delete your account? Once your
                    account is deleted, all of its resources and data will be
                    permanently deleted. Please enter your password to confirm
                    you would like to permanently delete your account.

                    <div class="mt-4">
                        <div class="block w-3/4">
                            <password-input
                                placeholder="Password"
                                ref="passwordInput"
                                v-model="form.password"
                                @keyup.enter="deleteUser"
                            />
                        </div>
                        <jet-input-error
                            :message="form.errors.password"
                            class="mt-2"
                        />
                    </div>
                </template>

                <template #footer>
                    <jet-secondary-button @click="closeModal">
                        Cancel
                    </jet-secondary-button>

                    <button
                        class="ml-2 error"
                        @click="deleteUser"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Delete Account
                    </button>
                </template>
            </jet-dialog-modal>
        </template>
    </jet-action-section>
</template>

<script setup>
import JetActionSection from "@/Jetstream/ProfileActionSection.vue";
import JetDialogModal from "@/Jetstream/DialogModal.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetSecondaryButton from "@/Jetstream/SecondaryButton.vue";
import PasswordInput from "@/Components/PasswordInput.vue";
import { useGymRevForm } from "@/utils";

const confirmingUserDeletion = ref(false);
const form = useGymRevForm({
    password: "",
});
const passwordInput = ref(null);

const confirmUserDeletion = () => {
    confirmingUserDeletion = true;

    setTimeout(() => passwordInput.$refs.input.focus(), 250);
};

const deleteUser = () => {
    form.delete(route("current-user.destroy"), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.$refs.input.focus(),
        onFinish: () => form.reset(),
    });
};

function closeModal() {
    confirmingUserDeletion = false;
    form.reset();
}
</script>
