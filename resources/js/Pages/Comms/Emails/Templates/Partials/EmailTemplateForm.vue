<template>
    <template v-if="shouldMount">
        <ApolloQuery :query="(gql) => queries.TOPOL_API_KEY">
            <template v-slot="{ result: { data } }">
                <email-builder
                    @onSave="handleOnSave"
                    @onSaveAndClose="handleOnSave"
                    @OnClose="handleOnClose"
                    :json="transformJson(emailTemplate?.json) || {}"
                    :title="emailTemplate?.name || 'New Email Template'"
                    :api-key="data.topolApiKey"
                />
            </template>
        </ApolloQuery>
        <daisy-modal ref="nameModal">
            <div class="form-control">
                <label for="name" class="label">Name</label>
                <input type="text" v-model="form.name" autofocus id="name" />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>

            <div class="form-control">
                <label for="subject" class="label">Subject Line</label>
                <input
                    type="text"
                    v-model="form.subject"
                    id="subject"
                    class="form-control w-full"
                />
                <jet-input-error :message="form.errors.subject" class="mt-2" />
            </div>

            <template #actions>
                <div class="flex-grow" />
                <Button
                    class="btn-secondary"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing || !form.isDirty || isProcessing"
                    :loading="form.processing || isProcessing"
                    @click="handleOperation"
                >
                    Save
                </Button>
            </template>
        </daisy-modal>

        <template v-if="isProcessing">
            <daisy-modal
                :closable="false"
                :open="true"
                :showCloseButton="false"
            >
                <Spinner />
            </daisy-modal>
        </template>
    </template>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useGymRevForm } from "@/utils";
import { Inertia } from "@inertiajs/inertia";
import Button from "@/Components/Button.vue";
import Spinner from "@/Components/Spinner.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import EmailBuilder from "@/Pages/Comms/Emails/Templates/Partials/EmailBuilder.vue";
import DaisyModal from "@/Components/DaisyModal.vue";
import queries from "@/gql/queries";
import { useMutation } from "@vue/apollo-composable";
import mutations from "@/gql/mutations";

const emit = defineEmits(["cancel", "close"]);

const props = defineProps({
    emailTemplate: {
        type: Object,
        default: {
            markup: null,
            json: null,
            thumbnail: null,
            name: null,
            subject: null,
        },
    },
    topolApiKey: {
        type: String,
        required: true,
    },
    useInertia: {
        type: Boolean,
        default: false,
    },
    editParam: {
        type: [String, Object],
        default: null,
    },
    createParam: {
        type: [String, Object],
        default: null,
    },
});

/** gives us the mutation type that will be ran as a string */
const usingCrudAction = computed(() => {
    if (props.editParam) return "update";
    if (props.createParam) return "create";
    return "unknown";
});

const { mutate: createEmailTemplate } = useMutation(
    mutations.emailTemplate.create
);
const { mutate: updateEmailTemplate } = useMutation(
    mutations.emailTemplate.update
);

const validInputFields = {
    create: ["name", "subject", "markup", "json"],
    update: ["id", "name", "subject", "markup", "json"],
    unknown: [],
};

const operation = ref(null); // reference to gql query that will be ran on save
const nameModal = ref(null); // DOM reference for template name and subject
const isProcessing = ref(false);
const form = useGymRevForm({
    ...props.emailTemplate,
});

/* since component is resource heavy and requires an api key, only load into memory if necessary */
const shouldMount = computed(() => {
    return Boolean(props.editParam || props.createParam);
});

/**
 * looks at which fields are valid inputs for the current operation  and returns an object with that shape & data supplied */
const getInputData = () => {
    let unprFormData = form.dirty().data();
    let fields = validInputFields[usingCrudAction.value];

    let inputFields = {};

    for (let i = 0; i < fields.length; i++) {
        inputFields[fields[i]] = unprFormData[fields[i]];
    }

    return inputFields;
};

const transformJson = (data) => JSON.parse(data);

/** initiates call to the current operation with the right fields and input data in the right format */
const handleOperation = async () => {
    isProcessing.value = true;
    let rawData = getInputData();

    const { data } = await operation.value({
        input: { ...rawData, json: JSON.stringify(rawData.json) },
    });

    let savedTemplate =
        data["createEmailTemplate"] ?? data["updateEmailTemplate"];

    isProcessing.value = false;
    emit("close", savedTemplate);
};

const handleOnSave = ({ html, json }) => {
    form.markup = html;
    form.json = json;
    //TODO: generate thumbnail
    // form.thumbnail = generateThumbnail(html);
    if (!form.name) {
        nameModal.value.open();
        return;
    }

    handleOperation();
};

const handleOnClose = (template) => {
    emit("cancel", template);
};

onMounted(() => {
    operation.value =
        typeof props.emailTemplate.name === "string"
            ? updateEmailTemplate
            : createEmailTemplate;
});
</script>
