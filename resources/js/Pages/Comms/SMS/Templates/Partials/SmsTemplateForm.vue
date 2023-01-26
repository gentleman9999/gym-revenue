<template>
    <jet-form-section @submitted="handleOperation">
        <template #form>
            <div class="form-control col-span-6">
                <label for="name" class="label">Name</label>
                <input
                    type="text"
                    :disabled="isProcessing"
                    v-model="form.name"
                    autofocus
                    id="name"
                />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>
            <div class="col-span-6">
                <sms-form-control
                    v-model="form.markup"
                    :disabled="isProcessing"
                    id="markup"
                    label="Template"
                />
                <jet-input-error :message="form.errors.markup" class="mt-2" />
            </div>
            <div
                class="form-control col-span-6 flex flex-row"
                v-if="canActivate"
            >
                <input
                    type="checkbox"
                    v-model="form.active"
                    id="active"
                    class="mt-2"
                    :value="true"
                />
                <label for="active" class="label ml-4"
                    >Activate (allows assigning to Campaigns)</label
                >
                <jet-input-error :message="form.errors.active" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <Button
                type="button"
                @click="$emit('cancel')"
                :class="{ 'opacity-25': isProcessing }"
                error
                outline
                :disabled="isProcessing"
            >
                Cancel
            </Button>
            <div class="flex-grow" />
            <Button
                class="btn-secondary"
                :class="{ 'opacity-25': isProcessing }"
                :disabled="isProcessing"
                :loading="isProcessing"
            >
                {{ operation }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script setup>
import { computed, ref } from "vue";

import JetFormSection from "@/Jetstream/FormSection.vue";
import Spinner from "@/Components/Spinner.vue";
import { useGymRevForm } from "@/utils";
import SmsFormControl from "@/Components/SmsFormControl.vue";
import Button from "@/Components/Button.vue";

import queries from "@/gql/queries";
import JetInputError from "@/Jetstream/InputError.vue";
import { useMutation } from "@vue/apollo-composable";
import mutations from "@/gql/mutations";

import { toastInfo, toastError } from "@/utils/createToast";

const emit = defineEmits(["cancel", "done"]);

const props = defineProps({
    smsTemplate: {
        type: Object,
        default: {
            name: "",
            markup: "",
            active: true,
        },
    },
    editParam: {
        type: [Object, null],
        default: null,
    },
});

// TODO: need to get this from gql somehow
const canActivate = ref(false);
const isProcessing = ref(false);

const { mutate: createSmsTemplate } = useMutation(mutations.smsTemplate.create);
const { mutate: updateSmsTemplate } = useMutation(mutations.smsTemplate.update);

let operation = computed(() => {
    return props.editParam !== null ? "Update" : "Create";
});

let operFn = computed(() => {
    return operation.value === "Update" ? updateSmsTemplate : createSmsTemplate;
});

const form = useGymRevForm(props.smsTemplate);

const handleOperation = async () => {
    try {
        isProcessing.value = true;
        let rawData = {
            id: form.id,
            name: form.name,
            markup: form.markup,
            active: form.active,
        };
        if (props.editParam === null) delete rawData.id;

        const { data } = await operFn.value({
            input: rawData,
        });

        let savedTemplate =
            data["createSmsTemplate"] ?? data["updateSmsTemplate"];

        toastInfo("SMS Template " + operation.value + "d!");
        isProcessing.value = false;
        emit("done", savedTemplate);
    } catch (error) {
        toastError("There was a problem updating the data - try again..");
        isProcessing.value = false;
        console.log("template operation error:", error);
    }
};
</script>
