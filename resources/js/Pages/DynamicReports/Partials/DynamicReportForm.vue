<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="col-span-6">
                <jet-label for="name" value="Name" />
                <input
                    id="name"
                    type="text"
                    class="block w-full mt-1"
                    v-model="form.name"
                />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>
            <!-- model selection -->
            <div class="col-span-6">
                <label for="selected_model">Model</label>
                <select
                    id="selected_model"
                    @change="clearStrings"
                    v-model="selectedModel"
                    class="mt-1 w-full form-select capitalize"
                >
                    <option disabled :value="null">Choose a model</option>
                    <option
                        class="capitalize"
                        v-for="model in models"
                        :value="model"
                    >
                        {{ model.name }}
                    </option>
                </select>
                <jet-input-error
                    :message="form.errors.selected_model"
                    class="mt-2"
                />
            </div>

            <!-- field entry -->
            <div
                class="mt-2 col-span-3"
                v-if="selectedModel !== null"
                v-for="field in selectedModel.stringFields"
            >
                <label class="block" :for="selectedModel.id + '-' + field">{{
                    field
                }}</label>
                <input
                    class="mt-1 w-full"
                    :id="selectedModel.id + '-' + field"
                    type="text"
                    v-model="selectedModelStringFields[field]"
                />
            </div>
        </template>

        <template #actions>
            <Button
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
                :disabled="form.processing"
                :loading="form.processing"
            >
                {{ operation }}
            </Button>
        </template>
    </jet-form-section>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useGymRevForm } from "@/utils";
import { useModal } from "@/Components/InertiaModal";
import { Inertia } from "@inertiajs/inertia";
import { resolveModel, models, parseFilterValues } from "./helpers";

import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";

const props = defineProps({
    clientId: {
        type: String,
        required: true,
    },
    report: {
        type: Object,
        default: {
            id: null,
            name: "",
            filters: "",
            model: null,
        },
    },
});

const selectedModel = ref(resolveModel(props?.report?.model));
const selectedModelName = computed(() => {
    return selectedModel?.value?.name ?? "";
});
const selectedModelStringFields = ref({});

const clearStrings = () => {
    selectedModelStringFields.value = {};
};

/** Generate the filter URL to use based on data input */
const generatedURL = computed(() => {
    let modelName = selectedModel?.value?.name;
    let textFields = [];

    for (let sf in selectedModelStringFields.value) {
        if (selectedModelStringFields.value[sf].trim() !== "") {
            textFields.push(
                `filter[${sf}]=${selectedModelStringFields.value[sf]}`
            );
        }
    }

    let textFieldsStr = textFields.join("&");

    return `/dynamicreports/${modelName}?${textFieldsStr}`;
});

let operation = props?.report?.id ? "Update" : "Create";

let report = {
    ...props.report,
    client_id: props.clientId,
    filters: generatedURL,
    model: selectedModelName,
};

const modal = useModal();
const form = useGymRevForm(report);

const handleSubmit = () => {
    if (operation === "Create") {
        form.post(route("dynamic-reports.store"), {
            onSuccess: () => $emit("saved"),
        });
    } else {
        form.put(route("dynamic-reports.update", report.id), {
            onSuccess: () => $emit("saved"),
        });
    }
};

const handleCancel = () => {
    if (modal?.value?.close) {
        modal.value.close();
        return;
    }
    Inertia.visit(route("dynamic-reports"));
};

onMounted(() => {
    let filterStr = props?.report?.filters;
    if (filterStr) {
        let matches = parseFilterValues(filterStr);

        let newStrFields = {};
        for (let i of matches) {
            newStrFields[i["field"]] = i["value"];
        }

        selectedModelStringFields.value = newStrFields;
    }
});
</script>
