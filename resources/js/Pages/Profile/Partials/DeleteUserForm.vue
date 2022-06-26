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
                                ref="password"
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

<script>
import { defineComponent } from "vue";
import JetActionSection from "@/Jetstream/ProfileActionSection";
import JetDialogModal from "@/Jetstream/DialogModal";

import JetInputError from "@/Jetstream/InputError";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import PasswordInput from "@/Components/PasswordInput";

export default defineComponent({
    components: {
        JetActionSection,

        JetDialogModal,

        JetInputError,
        JetSecondaryButton,
        PasswordInput,
    },

    data() {
        return {
            confirmingUserDeletion: false,

            form: this.$inertia.form({
                password: "",
            }),
        };
    },

    methods: {
        confirmUserDeletion() {
            this.confirmingUserDeletion = true;

            setTimeout(() => this.$refs.password.$refs.input.focus(), 250);
        },

        deleteUser() {
            this.form.delete(route("current-user.destroy"), {
                preserveScroll: true,
                onSuccess: () => this.closeModal(),
                onError: () => this.$refs.password.$refs.input.focus(),
                onFinish: () => this.form.reset(),
            });
        },

        closeModal() {
            this.confirmingUserDeletion = false;

            this.form.reset();
        },
    },
});
</script>
