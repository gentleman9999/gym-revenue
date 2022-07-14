<template>
    <span>
        <span @click="startConfirmingPassword">
            <slot />
        </span>

        <jet-dialog-modal :show="confirmingPassword" @close="closeModal">
            <template #title>
                {{ title }}
            </template>

            <template #content>
                {{ content }}

                <div class="mt-4">
                    <div class="w-3/4 mt-1">
                        <password-input
                            class="block w-full"
                            placeholder="Password"
                            ref="password"
                            v-model="form.password"
                            @keyup.enter="confirmPassword"
                        />
                    </div>
                    <jet-input-error :message="form.error" class="mt-2" />
                </div>
            </template>

            <template #footer>
                <jet-secondary-button @click="closeModal">
                    Cancel
                </jet-secondary-button>

                <Button
                    class="ml-2"
                    @click="confirmPassword"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ button }}
                </Button>
            </template>
        </jet-dialog-modal>
    </span>
</template>

<script setup>
import { ref, nextTick, defineEmits } from "vue";
import Button from "@/Components/Button.vue";
import JetDialogModal from "./DialogModal.vue";
import JetInputError from "./InputError.vue";
import JetSecondaryButton from "./SecondaryButton.vue";
import PasswordInput from "@/Components/PasswordInput.vue";

const props = defineProps({
    title: {
        type: String,
        default: "Confirm Password",
    },
    content: {
        type: String,
        default: "For your security, please confirm your password to continue.",
    },
    button: {
        type: String,
        default: "Confirm",
    },
});

const confirmingPassword = ref(false);
const password = ref(null);
const form = ref({
    password: "",
    error: "",
});
const emit = defineEmits(["confirmed"]);

const startConfirmingPassword = () => {
    axios.get(route("password.confirmation")).then((response) => {
        if (response.data.confirmed) {
            emit("confirmed");
        } else {
            confirmingPassword.value = true;
            setTimeout(() => password.$refs.input.focus(), 250);
        }
    });
};
const confirmPassword = () => {
    form.value.processing = true;

    axios
        .post(route("password.confirm"), {
            password: form.value.password,
        })
        .then(() => {
            form.value.processing = false;
            closeModal();
            nextTick(() => emit("confirmed"));
        })
        .catch((error) => {
            form.value.processing = false;
            form.value.error = error.response.data.errors.password[0];
            password.$refs.input.focus();
        });
};
const closeModal = () => {
    confirmingPassword.value = false;
    form.value.password = "";
    form.value.error = "";
};
</script>
