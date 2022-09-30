<template>
    <email-builder
        @onSave="handleOnSave"
        @onSaveAndClose="handleOnSave"
        @onClose="handleOnClose"
        :json="template?.json || null"
        :title="template?.name || undefined"
        :api-key="topolApiKey"
    />
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

<script>
import { ref } from "vue";
import { useGymRevForm } from "@/utils";
import { Inertia } from "@inertiajs/inertia";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import EmailBuilder from "@/Pages/Comms/Emails/Templates/Partials/EmailBuilder.vue";
import DaisyModal from "@/Components/DaisyModal.vue";
import usePage from "@/Components/InertiaModal/usePage";
import { useModal } from "@/Components/InertiaModal";

export default {
    components: {
        EmailBuilder,
        Button,
        JetFormSection,
        JetInputError,
        DaisyModal,
    },
    props: {
        template: {
            type: Object,
        },
        canActivate: {
            type: Boolean,
            required: true,
        },
        topolApiKey: {
            type: String,
            required: true,
        },
        useInertia: {
            type: Boolean,
            default: true,
        },
    },
    setup(props, { emit }) {
        const inertiaModal = useModal();
        const page = usePage();
        const nameModal = ref(null);
        const isProcessing = ref(false);
        let template = props?.template || {
            markup: null,
            json: null,
            thumbnail: null,
            name: null,
            subject: null,
            client_id: page.props.value.user?.client_id,
        };
        let operation = "Update";
        if (!props.template) {
            operation = "Create";
        }

        const form = useGymRevForm(template);
        const closeAfterSave = ref(false);

        let handleSubmit = () => {
            if (!form.processing) {
                if (props.useInertia) {
                    form.dirty().put(
                        route("mass-comms.email-templates.update", template.id),
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
                                template.id
                            ),
                            form.dirty().data()
                        )
                        .then(({ data }) => {
                            isProcessing.value = false;
                            emit("done", data);
                        });
                }
            }
        };
        if (operation === "Create") {
            handleSubmit = () => {
                if (!form.processing) {
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
                            .post(
                                route("mass-comms.email-templates.store"),
                                form.data()
                            )
                            .then(({ data }) => {
                                console.log("onSuccess-Create!");
                                isProcessing.value = false;
                                emit("done", data);
                            });
                    }
                }
            };
        }

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

        const handleOnSaveAndClose = ({ html, json }) => {
            closeAfterSave.value = true;
            handleOnSave({ html, json });
        };

        const handleOnClose = (template) => {
            if (props.useInertia) {
                if (inertiaModal.value?.close) {
                    inertiaModal.value?.close();
                } else {
                    //go back
                    Inertia.visit(route("mass-comms.email-templates"));
                }
            } else {
                emit("cancel", template);
            }
        };

        return {
            form,
            buttonText: operation,
            handleSubmit,
            nameModal,
            handleOnSave,
            handleOnSaveAndClose,
            handleOnClose,
            isProcessing,
        };
    },
};
</script>
