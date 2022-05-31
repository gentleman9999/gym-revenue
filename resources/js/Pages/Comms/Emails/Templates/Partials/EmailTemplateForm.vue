<template>
    <email-builder
        @onSave="handleOnSave"
        @onSaveAndClose="handleOnSaveAndClose"
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
        <!--                autofocus-->
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
                :disabled="form.processing"
                :loading="form.processing"
                @click="handleSubmit"
            >
                {{ buttonText }}
            </Button>
        </template>
    </daisy-modal>
</template>

<script>
import { ref } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetInputError from "@/Jetstream/InputError";
import SweetModal from "@/Components/SweetModal3/SweetModal";
import EmailBuilder from "@/Pages/Comms/Emails/Templates/Partials/EmailBuilder";
import DaisyModal from "@/Components/DaisyModal";
import usePage from "@/Components/InertiaModal/usePage";
import { useModal } from "@/Components/InertiaModal";

export default {
    components: {
        EmailBuilder,
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        SweetModal,
        DaisyModal,
    },
    props: ["clientId", "template", "canActivate", "topolApiKey"],
    setup(props, context) {
        const inertiaModal = useModal();
        const page = usePage();
        const nameModal = ref(null);
        let template = props?.template || {
            markup: null,
            json: null,
            thumbnail: null,
            name: null,
            subject: null,
            client_id: page.props.value.user?.current_client_id,
        };
        let operation = "Update";
        if (!props.template) {
            operation = "Create";
        }

        const form = useForm(template);
        const closeAfterSave = ref(false);

        let handleSubmit = () => {
            if (!form.processing) {
                form.put(route("comms.email-templates.update", template.id), {
                    // headers: { "X-Inertia-Modal-Redirect": true },
                    headers: { "X-Inertia-Modal-CloseOnSuccess": true },
                    onFinish: () => {
                        if (closeAfterSave.value) {
                            console.log(
                                "closeAfterSave",
                                closeAfterSave.value,
                                inertiaModal.value
                            );
                            inertiaModal.value.close();
                        }
                    },
                });
            }
        };
        if (operation === "Create") {
            handleSubmit = () => {
                if (!form.processing) {
                    form.post(route("comms.email-templates.store"), {
                        headers: { "X-Inertia-Modal-Redirect": true },
                        onSuccess: () => {
                            console.log("onSuccess-Create!");
                            if (closeAfterSave.value) {
                                console.log("close the modal now!");
                                inertiaModal.value.close();
                            }
                        },
                    });
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

        return {
            form,
            buttonText: operation,
            handleSubmit,
            nameModal,
            handleOnSave,
            handleOnSaveAndClose,
        };
    },
};
</script>
