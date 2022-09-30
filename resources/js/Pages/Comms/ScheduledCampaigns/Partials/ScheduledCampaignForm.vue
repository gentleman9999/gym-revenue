<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div
                class="form-control col-span-6"
                v-if="scheduledCampaign && !scheduledCampaign?.can_publish"
            >
                <div class="alert alert-warning">
                    Warning - This campaign has already launched! The only thing
                    you can update at this time is the "name" field.
                </div>
            </div>

            <div class="form-control col-span-6">
                <label for="name" class="label">Name</label>
                <input type="text" v-model="form.name" autofocus id="name" />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>

            <div class="form-control col-span-6 md:col-span-3 flex flex-col">
                <p>Select an audience</p>
                <select
                    v-model="form.audience_id"
                    :disabled="!canEditActiveInputs"
                    id="audience_id"
                >
                    <option
                        v-for="audience in audiences"
                        :value="audience.id"
                        :key="audience.id"
                    >
                        {{ audience.name }}
                    </option>
                </select>
                <jet-input-error
                    :message="form.errors.audience_id"
                    class="mt-2"
                />
            </div>

            <div class="form-control col-span-6 md:col-span-3 flex flex-col">
                <p>Select Communication Type</p>
                <select
                    v-model="form.template_type"
                    :disabled="!canEditActiveInputs"
                    id="template_type"
                >
                    <option
                        v-for="templateType in template_types"
                        :value="templateType.entity"
                        :key="templateType.entity"
                    >
                        {{ templateType.name }}
                    </option>
                </select>
                <jet-input-error
                    :message="form.errors.template_type"
                    class="mt-2"
                />
            </div>

            <div class="form-control col-span-6 md:col-span-3 flex flex-col">
                <p>Select a Template</p>
                <select
                    v-model="form.template_id"
                    :disabled="!canEditActiveInputs || !form?.template_type"
                    id="email_template_id"
                >
                    <template
                        v-if="
                            form.template_type?.toLowerCase().includes('email')
                        "
                    >
                        <option
                            v-for="template in emailTemplates"
                            :value="template.id"
                            :key="template.id"
                        >
                            {{ template.name }}
                        </option>
                    </template>
                    <template
                        v-else-if="
                            form.template_type?.toLowerCase().includes('sms')
                        "
                    >
                        <option
                            v-for="template in smsTemplates"
                            :value="template.id"
                            :key="template.id"
                        >
                            {{ template.name }}
                        </option>
                    </template>
                </select>
                <jet-input-error
                    :message="form.errors.template_id"
                    class="mt-2"
                />
            </div>

            <div class="form-control col-span-6 md:col-span-3 flex flex-col">
                <p>When should this campaign be sent?</p>
                <date-picker
                    v-model="form.send_at"
                    dark
                    :disabled="!canEditActiveInputs"
                    :min-date="new Date()"
                    :month-change-on-scroll="false"
                    :auto-apply="true"
                    :close-on-scroll="true"
                />
                <jet-input-error :message="form.errors.send_at" class="mt-2" />
            </div>
            <div class="form-control col-span-6 flex flex-row">
                <input
                    type="checkbox"
                    v-model="form.is_published"
                    :disabled="!canEditActiveInputs"
                    autofocus
                    id="active"
                    class="mt-2"
                />
                <label for="active" class="label ml-4">Publish</label>
                <jet-input-error
                    :message="form.errors.is_published"
                    class="mt-2"
                    v-model="form.is_published"
                />
            </div>
        </template>

        <template #actions>
            <!--            TODO: navigation links should always be Anchors. We need to extract button css so that we can style links as buttons-->
            <Button
                type="button"
                @click="$inertia.visit(route('mass-comms.scheduled-campaigns'))"
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
                type="button"
                @click.prevent="submitForm"
            >
                {{ buttonText }}
            </Button>
        </template>
    </jet-form-section>

    <confirm
        v-if="buttonText === 'Update' && showConfirm"
        title="Are you sure?"
        @confirm="
            submitForm();
            showConfirm = false;
        "
        @cancel="showConfirm = false"
    >
        {{ modalText }}
    </confirm>
</template>

<script>
import { computed, ref, onBeforeUnmount, onMounted } from "vue";
import { useForm, usePage } from "@inertiajs/inertia-vue3";
import { Inertia } from "@inertiajs/inertia";
import SmsFormControl from "@/Components/SmsFormControl.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import Confirm from "@/Components/Confirm.vue";
import DatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import Multiselect from "@vueform/multiselect";
import { getDefaultMultiselectTWClasses, getFormObjectFromData } from "@/utils";
import { toastWarning } from "@/utils/createToast";

export default {
    components: {
        Button,
        JetFormSection,
        SmsFormControl,
        JetInputError,
        Confirm,
        DatePicker,
        Multiselect,
    },
    props: [
        "scheduledCampaign",
        "canActivate",
        "template_type",
        "template_types",
        "smsTemplates",
        "emailTemplates",
        "audiences",
    ],
    setup(props, context) {
        const page = usePage();
        const modal = ref(null);

        const formFields = [
            "name",
            "audience_id",
            "template_id",
            "template_type",
            "send_at",
            "is_published",
        ];

        const { operation, object: campaign } = getFormObjectFromData(
            formFields,
            props?.scheduledCampaign
        );

        if (operation === "Create") {
            campaign.is_published = false;
        }

        const form = useForm(campaign);

        const handleCampaignLaunched = () => {
            toastWarning("Woops! This campaign has launched.  Reloading...");
            Inertia.reload({ preserveState: true, preserveScroll: true });
        };

        let timeoutId;
        onMounted(() => {
            if (
                props.scheduledCampaign &&
                props.scheduledCampaign.status === "PENDING"
            ) {
                const timeTillLaunch =
                    new Date(props.scheduledCampaign.send_at) - new Date();
                if (timeTillLaunch > 0) {
                    console.log("set a timer for ", timeTillLaunch);
                    timeoutId = setTimeout(
                        handleCampaignLaunched,
                        timeTillLaunch
                    );
                }
            }
        });
        onBeforeUnmount(() => {
            if (timeoutId) {
                clearTimeout(timeoutId);
            }
        });

        const transformDate = (date) => {
            if (!date?.toISOString) {
                return date;
            }

            return date.toISOString().slice(0, 19).replace("T", " ");
        };

        const transformData = (data) => {
            data.send_at = transformDate(data.send_at);
            return data;
        };

        let handleSubmit = () => {
            form.transform(transformData).put(
                route(
                    "mass-comms.scheduled-campaigns.update",
                    props.scheduledCampaign.id
                )
            );
        };
        if (operation === "Create") {
            handleSubmit = () =>
                form
                    .transform(transformData)
                    .post(route("mass-comms.scheduled-campaigns.store"));
        }
        const canEditActiveInputs = computed(() => {
            if (operation === "Create") {
                return true;
            }
            return props.scheduledCampaign?.can_publish;
        });

        return {
            form,
            buttonText: operation,
            handleSubmit,
            modal,
            canEditActiveInputs,
            availableAudiences: page.props.value.availableAudiences,
            availableEmailTemplates: page.props.value.availableEmailTemplates,
            multiselectClasses: getDefaultMultiselectTWClasses(),
        };
    },
    data() {
        return {
            modalText: "",
            showConfirm: false,
        };
    },
    methods: {
        submitForm(active) {
            this.handleSubmit();
        },
        closeModal() {
            this.$refs.modal.close();
        },
    },
};
</script>

<style scoped></style>
