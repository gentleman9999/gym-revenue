<template>
    <template v-if="shouldMount">
        <ApolloQuery :query="(gql) => queries.TOPOL_API_KEY">
            <template v-slot="{ result: { data } }">
                <email-builder
                    @onSave="handleOnSave"
                    @onSaveAndClose="handleOnSave"
                    @onClose="handleOnClose"
                    :json="emailTemplate?.json || {}"
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
            <!--        TODO: we may need to remove subject from email templates. depends on design-->
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

            <!--        <div class="form-control col-span-6 flex flex-row" v-if="canActivate">-->
            <!--            <input-->
            <!--                type="checkbox"-->
            <!--                v-model="form.active"-->
            <!--                id="active"-->
            <!--                class="mt-2"-->
            <!--                :value="true"-->
            <!--            />-->
            <!--            <label for="active" class="label ml-4"-->
            <!--                >Activate (allows assigning to Campaigns)</label-->
            <!--            >-->
            <!--            <jet-input-error :message="form.errors.active" class="mt-2" />-->
            <!--        </div>-->
            <template #actions>
                <div class="flex-grow" />
                <Button
                    class="btn-secondary"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing || !form.isDirty || isProcessing"
                    :loading="form.processing || isProcessing"
                    @click="handleSubmit"
                >
                    {{ buttonText }}
                </Button>
            </template>
        </daisy-modal>
    </template>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useGymRevForm } from "@/utils";
import { Inertia } from "@inertiajs/inertia";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import EmailBuilder from "@/Pages/Comms/Emails/Templates/Partials/EmailBuilder.vue";
import DaisyModal from "@/Components/DaisyModal.vue";
import usePage from "@/Components/InertiaModal/usePage";
import { useModal } from "@/Components/InertiaModal";
import queries from "@/gql/queries";

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

const handleOperationUpdate = () => {
    if (props.useInertia) {
        form.dirty().put(
            route("mass-comms.email-templates.update", props.emailTemplate.id),
            {
                headers: { "X-Inertia-Modal-Redirect": true },
                // headers: { "X-Inertia-Modal-CloseOnSuccess": true },
                onFinish: ({ data }) => {
                    emit("done", data);
                },
            }
        );
    } else {
        isProcessing.value = true;
        axios
            .put(
                route(
                    "mass-comms.email-templates.update",
                    props.emailTemplate.id
                ),
                form.dirty().data()
            )
            .then(({ data }) => {
                isProcessing.value = false;
                emit("done", data);
            });
    }
};

const handleOperationCreate = () => {
    if (props.useInertia) {
        form.post(route("mass-comms.email-templates.store"), {
            headers: { "X-Inertia-Modal-Redirect": true },
            onSuccess: ({ data }) => {
                emit("done", data);
            },
        });
    } else {
        isProcessing.value = true;
        axios
            .post(route("mass-comms.email-templates.store"), form.data())
            .then(({ data }) => {
                console.log("onSuccess-Create!");
                isProcessing.value = false;
                emit("done", data);
            });
    }
};

const handleSubmit = () => {
    if (operation.value === "Create") return handleOperationCreate();
    if (operation.value === "Update") return handleOperationUpdate();
};

const handleOnSave = ({ html, json }) => {
    console.log("handleOnSave");
    form.markup = html;
    form.json = json;
    //TODO: generate thumbnail
    // form.thumbnail = generateThumbnail(html);
    if (!form.name) {
        nameModal.value.open();
        return;
    }
    handleSubmit();
};

const handleOnClose = (template) => {
    console.log("handle on close called");
    if (props.useInertia) {
        //go back
        Inertia.visit(route("mass-comms.email-templates"));
    } else {
        emit("cancel", template);
    }
};
</script>
