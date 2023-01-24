<template>
    <ApolloQuery :query="(gql) => queries.TOPOL_API_KEY">
        <template v-slot="{ result: { data } }">
            <email-builder
                @onSave="handleOnSave"
                @onSaveAndClose="handleOnSave"
                @OnClose="handleOnClose"
                :json="transformJson(template?.json) || {}"
                :title="template?.name || 'New Email Template'"
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
                :class="{ 'opacity-25': isProcessing }"
                :disabled="isProcessing"
                :loading="isProcessing"
                @click="handleOperation"
            >
                Save
            </Button>
        </template>
    </daisy-modal>

    <template v-if="isProcessing">
        <daisy-modal :closable="false" :open="true" :showCloseButton="false">
            <Spinner />
        </daisy-modal>
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
import { toastInfo, toastError } from "@/utils/createToast";

const emit = defineEmits(["cancel", "done"]);

const props = defineProps({
    template: {
        type: Object,
        default: {
            id: null,
            markup: null,
            json: null,
            thumbnail: null,
            name: null,
            subject: null,
        },
    },
    emailTemplate: {
        type: Object,
        default: {
            id: null,
            markup: null,
            json: null,
            thumbnail: null,
            name: null,
            subject: null,
        },
    },
    editParam: {
        type: [Object, null],
        default: null,
    },
    data: {
        type: Object,
    },
});

const { mutate: createEmailTemplate } = useMutation(
    mutations.emailTemplate.create
);
const { mutate: updateEmailTemplate } = useMutation(
    mutations.emailTemplate.update
);

const operation = computed(() => {
    return props.editParam !== null ? "Update" : "Create";
});

const operFn = computed(() => {
    return operation.value === "Update"
        ? updateEmailTemplate
        : createEmailTemplate;
});

const nameModal = ref(null); // DOM reference for template name and subject
const isProcessing = ref(false);

const form = useGymRevForm(props.template);

const transformJson = (data) => {
    if (!data) return {};
    return JSON.parse(data);
};

/** initiates call to the current operation with the right fields and input data in the right format */
const handleOperation = async () => {
    try {
        isProcessing.value = true;

        let rawData = {
            id: form.id,
            name: form.name,
            subject: form.subject,
            markup: form.markup,
            json: JSON.stringify(form.json),
        };
        if (props.editParam === null) delete rawData.id;

        const { data } = await operFn.value({
            input: rawData,
        });

        let savedTemplate =
            data["createEmailTemplate"] ?? data["updateEmailTemplate"];

        toastInfo("Email Template " + operation.value + "d!");
        isProcessing.value = false;
        emit("done", savedTemplate);
    } catch (error) {
        toastError("There was a problem updating the data - try again..");
        isProcessing.value = false;
        console.log("template operation error:", error);
    }
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
</script>
