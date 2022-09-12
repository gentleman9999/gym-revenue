<template>
    <LayoutHeader title="Reporting Dashboard">Dynamic Reporting </LayoutHeader>

    <jet-bar-container>
        <jet-form-section @submitted="handleSubmit">
            <template #form>
                <div class="col-span-6">
                    <jet-label for="selected_model" value="selected_model" />
                    <select
                        id="selected_model"
                        @change="clearStrings"
                        v-model="selectedModel"
                        class="mt-1 w-full form-select capitalize"
                    >
                        <option :value="null" />
                        <option
                            class="capitalize"
                            v-for="model in models"
                            :value="model"
                        >
                            {{ model.name }}
                        </option>
                        <!-- <option value="user">Users</option>
                        <option value="lead">Leads</option> -->
                    </select>
                    <jet-input-error
                        :message="form.errors.selected_model"
                        class="mt-2"
                    />

                    <div
                        class="mt-4"
                        v-if="selectedModel !== null"
                        v-for="field in selectedModel.stringFields"
                    >
                        <label
                            class="block"
                            :for="selectedModel.id + '-' + field"
                            >{{ field }}</label
                        >
                        <input
                            :id="selectedModel.id + '-' + field"
                            type="text"
                            v-model="selectedModelStringFields[field]"
                        />
                    </div>
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
                    Submit
                </Button>
            </template>
        </jet-form-section>
    </jet-bar-container>

    <table
        class="table-fixed border-separate border-spacing-2 border border-slate-500"
    >
        <thead>
            <tr>
                <th v-for="field in fields">
                    {{ field }}
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="row in report">
                <td v-for="field in fields">
                    {{ row[field] }}
                </td>
            </tr>
        </tbody>
    </table>
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import { useGymRevForm } from "@/utils/index.js";
import { computed, ref } from "vue";

const props = defineProps({
    clientId: {
        type: String,
        required: true,
    },
    reportFilter: {
        type: Object,
    },
    report: {
        type: Array,
    },
});

const selectedModel = ref(null);
const selectedModelStringFields = ref({});

const models = ref([
    {
        id: "users",
        name: "users",
        stringFields: ["email", "first_name", "last_name", "alternate_email"],
    },
    {
        id: "leads",
        name: "leads",
        stringFields: ["email", "first_name", "last_name"],
    },
    {
        id: "members",
        name: "members",
        stringFields: ["email", "first_name", "last_name"],
    },
    {
        id: "leadexport",
        name: "leadexport",
        stringFields: ["email", "first_name", "last_name"],
    },
]);

const clearStrings = () => {
    selectedModelStringFields.value = {};
};

const handleCreateUrl = () => {
    let modelName = selectedModel.value.name;
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
};

let reportFilter = props.reportFilter;
if (!reportFilter) {
    reportFilter = {
        selected_model: "",
    };
}
const form = useGymRevForm(reportFilter);

const array = Object.entries(props.report);
const fields = computed(() =>
    Object.keys(props.report?.length ? props.report[0] : [])
);

let handleSubmit = () => {
    // form.post(route("dr.create"));
    const theUrlToUse = handleCreateUrl();
    console.log("location NOW", window.location);
    window.location = theUrlToUse;
    console.log("theURL", theUrlToUse);
};
</script>

<style scoped></style>
