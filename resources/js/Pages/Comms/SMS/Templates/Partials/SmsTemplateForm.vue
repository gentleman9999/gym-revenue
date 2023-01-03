<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="form-control col-span-6">
                <label for="name" class="label">Name</label>
                <input type="text" v-model="form.name" autofocus id="name" />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>
            <div class="col-span-6">
                <sms-form-control
                    v-model="form.markup"
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
                v-if="useInertia"
                type="button"
                @click="handleCancel"
                :class="{ 'opacity-25': form.processing }"
                error
                outline
                :disabled="form.processing"
            >
                Cancel
            </Button>
            <div class="flex-grow" />
            <Button
                class="btn-secondary"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing || !form.isDirty"
                :loading="form.processing"
            >
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script setup>
import { computed } from "vue";

import JetFormSection from "@/Jetstream/FormSection.vue";

import { useGymRevForm } from "@/utils";
import SmsFormControl from "@/Components/SmsFormControl.vue";
import Button from "@/Components/Button.vue";
import JetInputError from "@/Jetstream/InputError.vue";

const props = defineProps({
    smsTemplate: {
        type: Object,
        default: {
            markup: null,
            name: null,
            active: false,
        },
    },
    canActivate: {
        type: Boolean,
        required: true,
    },
});

const emit = defineEmits(["done", "error"]);

let operation = computed(() => {
    return typeof props.smsTemplate.name === "string" ? "Update" : "Create";
});

const form = useGymRevForm(props.smsTemplate);

let handleSubmit = () => {
    let endpoint = operation.value === "Update" ? "update" : "store";
    let crudOper = operation.value === "Update" ? "put" : "post";
    let axiosFn = axios[crudOper];

    axiosFn(route("mass-comms.sms-templates." + endpoint), form.data())
        .then(({ data }) => {
            emit("done", data);
        })
        .catch((err) => {
            emit("error", err);
        });
};

const handleCancel = () => {
    Inertia.visit(route("mass-comms.sms-templates"));
};
</script>
