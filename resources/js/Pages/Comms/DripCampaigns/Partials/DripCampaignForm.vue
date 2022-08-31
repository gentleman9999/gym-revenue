<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div
                class="form-control col-span-6"
                v-if="dripCampaign && !dripCampaign?.can_publish"
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
                    :message="form.errors.audiences"
                    class="mt-2"
                />
            </div>

            <div class="form-control md:col-span-3 col-span-6 flex flex-col">
                <p>When should this campaign start?</p>
                <date-picker
                    v-model="form.start_at"
                    dark
                    :disabled="!canEditActiveInputs"
                    :min-date="new Date()"
                    :month-change-on-scroll="false"
                    :auto-apply="true"
                    :close-on-scroll="true"
                />
                <jet-input-error :message="form.errors.start_at" class="mt-2" />
            </div>

            <div class="form-control md:col-span-3 col-span-6 flex flex-col">
                <p>Does this drip campaign have an end date?</p>
                <date-picker
                    v-model="form.end_at"
                    dark
                    :disabled="!canEditActiveInputs"
                    :min-date="new Date()"
                    :month-change-on-scroll="false"
                    :auto-apply="true"
                    :close-on-scroll="true"
                />
                <jet-input-error :message="form.errors.end_at" class="mt-2" />
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
            <Button
                type="button"
                @click="$inertia.visit(route('comms.drip-campaigns'))"
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
import { computed, onBeforeUnmount, onMounted, ref } from "vue";
import { useForm, usePage } from "@inertiajs/inertia-vue3";
import SmsFormControl from "@/Components/SmsFormControl.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import Confirm from "@/Components/Confirm.vue";
import DatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import Multiselect from "@vueform/multiselect";
import { getDefaultMultiselectTWClasses, getFormObjectFromData } from "@/utils";

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
    props: ["dripCampaign", "canActivate", "audiences"],
    setup(props, context) {
        const page = usePage();
        const modal = ref(null);

        const formFields = [
            "name",
            "audience_id",
            "start_at",
            "end_at",
            "is_published",
        ];

        const { operation, object: campaign } = getFormObjectFromData(
            formFields,
            props?.dripCampaign
        );

        if (operation === "Create") {
            campaign.is_published = false;
        }

        const form = useForm(campaign);

        onMounted(() => {
            if (props.dripCampaign && props.dripCampaign.status === "PENDING") {
                const timeTillLaunch =
                    new Date(props.dripCampaign.start_at) -
                    new Date().getTime();
                console.log({ timeTillLaunch });
            }
        });
        onBeforeUnmount(() => {});

        const transformDate = (date) => {
            if (!date?.toISOString) {
                return date;
            }

            return date.toISOString().slice(0, 19).replace("T", " ");
        };

        const transformData = (data) => {
            data.start_at = transformDate(data.start_at);
            data.end_at = transformDate(data.end_at);
            return data;
        };

        let handleSubmit = () => {
            form.transform(transformData).put(
                route("comms.drip-campaigns.update", props.dripCampaign.id)
            );
        };
        if (operation === "Create") {
            handleSubmit = () =>
                form
                    .transform(transformData)
                    .post(route("comms.drip-campaigns.store"));
        }

        const canEditActiveInputs = computed(() => {
            if (operation === "Create") {
                return true;
            }
            return props.dripCampaign?.can_publish;
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
