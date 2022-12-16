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
import usePage from "@/Components/InertiaModal/usePage";
import queries from "@/gql/queries";

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

const operation = ref("Create");
const page = usePage();
const nameModal = ref(null);
const isProcessing = ref(false);
const form = useGymRevForm({
    ...props.emailTemplate,
    client_id: page.props.value.user?.client_id,
});

const shouldMount = computed(() => {
    return Boolean(props.editParam || props.createParam);
});

onMounted(() => {
    if (typeof props.emailTemplate.name === "string") {
        operation.value = "Update";
    }
});

const transformJson = (data) => JSON.parse(data);

const onOperationDone = (data) => {
    isProcessing.value = false;
    emit("close", data);
};

const handleOperation = () => {
    let endpoint = operation.value === "Create" ? "store" : "update";
    let routeName = `mass-comms.email-templates.${endpoint}`;
    isProcessing.value = true;

    axios[endpoint === "store" ? "post" : "put"](
        route(routeName, props.emailTemplate.id),
        form.dirty().data()
    ).then(({ data }) => onOperationDone(data));
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
    console.log("emitting cancel");
    emit("cancel", template);
};
</script>
