<template>
    <jet-form-section @submitted="handleSubmit">
        <!--        <template #title>-->
        <!--            Location Details-->
        <!--        </template>-->

        <!--        <template #description>-->
        <!--            {{ buttonText }} a location.-->
        <!--        </template>-->
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
            <!--                <input id="client_id" type="hidden" v-model="form.client_id"/>-->
            <!--                <jet-input-error :message="form.errors.client_id" class="mt-2"/>-->
        </template>

        <template #actions>
            <!--            TODO: navigation links should always be Anchors. We need to extract button css so that we can style links as buttons-->
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

<script>
import { onMounted } from "vue";
import { useGymRevForm } from "@/utils";
import SmsFormControl from "@/Components/SmsFormControl.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import { useModal } from "@/Components/InertiaModal";
import { Inertia } from "@inertiajs/inertia";

export default {
    components: {
        Button,
        JetFormSection,
        SmsFormControl,
        JetInputError,
    },
    props: {
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
        useInertia: {
            type: Boolean,
            default: true,
        },
    },
    setup(props, { emit }) {
        onMounted(() => {
            console.log(
                "mounted sms template form with data:",
                props.smsTemplate
            );
            console.log("entire sms template form props: ", props);
        });

        let operation =
            typeof props.smsTemplate.name === "string" ? "Update" : "Create";

        const form = useGymRevForm(props.smsTemplate);

        let handleSubmit = () => {
            if (props.useInertia) {
                form.dirty().put(
                    route(
                        "mass-comms.sms-templates.update",
                        props.smsTemplate.id
                    )
                );
            } else {
                axios
                    .put(
                        route(
                            "mass-comms.sms-templates.update",
                            props.smsTemplate.id
                        ),
                        form.dirty().data()
                    )
                    .then(({ data }) => {
                        console.log("closeAfterSave", data);
                        emit("done", data);
                    })
                    .catch((err) => {
                        emit("error", err);
                    });
            }
        };
        if (operation === "Create") {
            if (props.useInertia) {
                handleSubmit = () =>
                    form
                        .post(route("mass-comms.sms-templates.store"))
                        .then(({ data }) => {
                            console.log("closeAfterSave", data);
                            emit("done", data);
                        })
                        .catch((err) => {
                            emit("error", err);
                        });
            } else {
                handleSubmit = () =>
                    axios
                        .post(
                            route("mass-comms.sms-templates.store"),
                            form.data()
                        )
                        .then(({ data }) => {
                            console.log("closeAfterSave", data);
                            emit("done", data);
                        })
                        .catch((err) => {
                            emit("error", err);
                        });
            }
        }

        const inertiaModal = useModal();
        const handleCancel = () => {
            if (inertiaModal?.value?.close) {
                inertiaModal.value.close();
            }
            Inertia.visit(route("mass-comms.sms-templates"));
        };
        return { form, buttonText: operation, handleSubmit, handleCancel };
    },
};
</script>
