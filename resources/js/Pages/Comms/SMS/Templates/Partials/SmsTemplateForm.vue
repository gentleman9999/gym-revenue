<template>
    <jet-form-section @submitted="handleOperation">
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

const props = defineProps({
    template: {
        type: Object,
        default: {
            name: "",
            markup: "",
            active: true,
        },
    },
    editParam: {
        type: [String, Object],
        default: null,
    },
});

// TODO: need to get this from gql somehow
const canActivate = ref(false);
const isProcessing = ref(false);

const { mutate: createSmsTemplate } = useMutation(mutations.smsTemplate.create);
const { mutate: updateSmsTemplate } = useMutation(mutations.smsTemplate.update);

const emit = defineEmits(["cancel", "done"]);

let operation = computed(() => {
    return props.editParam !== null ? "Update" : "Create";
});

let operFn = computed(() => {
    return operation.value === "Update" ? updateSmsTemplate : createSmsTemplate;
});

const form = useGymRevForm(props.template);

const handleOperation = async () => {
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

    let savedTemplate = data["createSmsTemplate"] ?? data["updateSmsTemplate"];

    isProcessing.value = false;
    emit("done", savedTemplate);
};

// let handleSubmit = () => {
//     let endpoint = operation.value === "Update" ? "update" : "store";
//     let crudOper = operation.value === "Update" ? "put" : "post";
//     let axiosFn = axios[crudOper];

//     axiosFn(route("mass-comms.sms-templates." + endpoint), form.data())
//         .then(({ data }) => {
//             emit("done", data);
//         })
//         .catch((err) => {
//             emit("error", err);
//         });
// };

// const handleCancel = () => {
//     Inertia.visit(route("mass-comms.sms-templates"));
// };
</script>
