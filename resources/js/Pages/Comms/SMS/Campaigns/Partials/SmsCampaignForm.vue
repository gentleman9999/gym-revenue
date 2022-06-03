<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="form-control col-span-6">
                <label for="name" class="label">Name</label>
                <input type="text" v-model="form.name" autofocus id="name" />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>

            <div
                class="form-control col-span-6 flex flex-row"
                v-if="canActivate"
            >
                <input
                    type="checkbox"
                    v-model="form.active"
                    autofocus
                    id="active"
                    class="mt-2"
                />
                <label for="active" class="label ml-4"
                    >Activate (allows assigning to Campaigns)</label
                >
                <jet-input-error
                    :message="form.errors.active"
                    class="mt-2"
                    v-model="form.active"
                />
            </div>

            <div
                class="form-control col-span-3 flex flex-col"
                v-if="form.active"
            >
                <p>Select an audience</p>
                <multiselect
                    v-model="form.audiences"
                    class="py-2"
                    :disabled="!canEditActiveInputs"
                    id="audiences"
                    mode="tags"
                    :close-on-select="false"
                    :create-option="true"
                    :options="
                        availableAudiences.map((audience) => ({
                            label: audience.name,
                            value: audience.id,
                        }))
                    "
                    :classes="multiselectClasses"
                />
                <jet-input-error
                    :message="form.errors.audiences"
                    class="mt-2"
                />
            </div>

            <div
                class="form-control col-span-3 flex flex-col"
                v-if="form.active"
            >
                <p>Select an SMS Template</p>
                <multiselect
                    v-model="form.sms_templates"
                    class="py-2"
                    :disabled="!canEditActiveInputs"
                    id="sms_templates"
                    mode="tags"
                    :close-on-select="false"
                    :create-option="true"
                    :options="
                        availableSmsTemplates.map((sms_templates) => ({
                            label: sms_templates.name,
                            value: sms_templates.id,
                        }))
                    "
                    :classes="multiselectClasses"
                />
                <jet-input-error
                    :message="form.errors.sms_templates"
                    class="mt-2"
                />
            </div>

            <div
                class="form-control col-span-3 flex flex-col"
                v-if="form.active"
            >
                <p>Select a firing schedule</p>
                <select
                    v-model="form.schedule"
                    class="py-2"
                    :disabled="!canEditActiveInputs"
                >
                    <option value="">Available Schedules</option>
                    <option value="drip">As Users are Added (Drip)</option>
                    <option value="bulk">All Subscribed Users (Bulk)</option>
                </select>
                <jet-input-error :message="form.errors.schedule" class="mt-2" />
            </div>

            <div
                class="form-control col-span-3 flex flex-col"
                v-if="form.active && form.schedule === 'bulk'"
            >
                <p>When should we trigger this SMS?</p>
                <div class="flex flex-row gap-8 h-16">
                    <label class="label">
                        <span class="label-text mr-2">Now</span>
                        <input
                            type="radio"
                            name="scheduleNow"
                            class="radio"
                            v-model="scheduleNow"
                            :disabled="!canEditActiveInputs"
                        />
                    </label>
                    <label class="label">
                        <span class="label-text mr-2">Later</span>
                        <input
                            type="radio"
                            name="scheduleNow"
                            :value="false"
                            class="radio"
                            v-model="scheduleNow"
                            :disabled="!canEditActiveInputs"
                        />
                    </label>
                </div>
                <date-picker
                    v-model="form.schedule_date"
                    dark
                    :disabled="!canEditActiveInputs"
                    v-if="!scheduleNow"
                    :month-change-on-scroll="false"
                    :auto-apply="true"
                    :close-on-scroll="true"
                    :min-date="
                        new Date(new Date().valueOf() - 1000 * 60 * 60 * 24)
                    "
                />
                <!--                <select v-model="form.schedule_date" class="py-2">-->
                <!--                    <option value="">Available Triggers</option>-->
                <!--                    <option value="now">Now</option>-->
                <!--                    <option value="1HOUR">1hr</option>-->
                <!--                </select>-->
                <jet-input-error
                    :message="form.errors.schedule_date"
                    class="mt-2"
                />
            </div>

            <!--                <input id="client_id" type="hidden" v-model="form.client_id"/>-->
            <!--                <jet-input-error :message="form.errors.client_id" class="mt-2"/>-->
        </template>

        <template #actions>
            <!--            TODO: navigation links should always be Anchors. We need to extract button css so that we can style links as buttons-->
            <Button
                type="button"
                @click="$inertia.visit(route('comms.sms-campaigns'))"
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
                @click.prevent="
                    buttonText === 'Update' ? warnFirst() : submitForm()
                "
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
import { computed, ref } from "vue";
import { useForm, usePage } from "@inertiajs/inertia-vue3";
import SmsFormControl from "@/Components/SmsFormControl";
import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetInputError from "@/Jetstream/InputError";
import Confirm from "@/Components/Confirm";
import DatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import Multiselect from "@vueform/multiselect";
import { getDefaultMultiselectTWClasses } from "@/utils";

export default {
    name: "SmsCampaignForm",
    components: {
        AppLayout,
        Button,
        JetFormSection,
        SmsFormControl,
        JetInputError,
        Confirm,
        DatePicker,
        Multiselect,
    },
    props: [
        "clientId",
        "campaign",
        "canActivate",
        "templates",
        "assignedTemplate",
        "assignedAudience",
    ],
    setup(props, context) {
        const page = usePage();
        const modal = ref(null);
        const scheduleNow = ref(
            isNaN(Date.parse(props.campaign?.schedule_date?.value))
        );
        let campaign = props.campaign;
        let operation = "Update";
        if (!campaign) {
            campaign = {
                name: null,
                active: false,
                audiences: [],
                sms_templates: [],
                schedule: "",
                schedule_date: "",
                // client_id: props.clientId
            };
            operation = "Create";
        } else {
            campaign["schedule_date"] = campaign.schedule_date?.value || "now";
            campaign["schedule"] = campaign.schedule?.value || "";

            campaign["sms_templates"] = page.props.value.smsTemplates.map(
                (template_id) => template_id.value
            );
            campaign["audiences"] = page.props.value.audiences.map(
                (audience_id) => audience_id.value
            );
        }

        console.log("campaign Params", campaign);
        const form = useForm(campaign);

        let handleSubmit = () => {
            form.transform((data) => ({
                ...data,
                schedule_date: scheduleNow.value ? "now" : data.schedule_date,
            })).put(route("comms.sms-campaigns.update", campaign.id));
        };
        if (operation === "Create") {
            handleSubmit = () => form.post(route("comms.sms-campaigns.store"));
        }

        // const canEditActiveInputs = !props.campaign?.schedule_date;
        // console.log({canEditActiveInputs: canEditActiveInputs});
        // const canEditActiveInputs = computed(() => !props.campaign.active && (!props.campaign?.schedule_date || new Date() < new Date(props.campaign.schedule_date)));
        const canEditActiveInputs = computed(() => {
            if (!props.campaign?.active) {
                return true;
            }
            return (
                !props.campaign?.schedule_date ||
                new Date() < new Date(`${props.campaign.schedule_date} UTC`)
            );
        });

        return {
            form,
            buttonText: operation,
            handleSubmit,
            modal,
            canEditActiveInputs,
            scheduleNow,
            availableAudiences: page.props.value.availableAudiences,
            availableSmsTemplates: page.props.value.availableSmsTemplates,
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
        warnFirst() {
            if (this.form.active) {
                // do some validation on the form
                let ready =
                    this.form["audience_id"] !== "" &&
                    this.form["schedule_date"] !== "" &&
                    this.form["schedule"] !== "" &&
                    this.form["sms_template_id"] !== "";

                if (ready) {
                    if (this.scheduleNow) {
                        this.modalText =
                            "Are you sure you are ready to launch this Campaign? You won't be able to edit it afterwards.";
                    } else {
                        this.modalText =
                            "If you continue, you WILL be able to update or cancel this launch until the campaign time.";
                    }
                } else {
                    if (
                        this.form["audience_id"] === "" &&
                        this.form["sms_template_id"] === ""
                    ) {
                        this.modalText =
                            "You did not set an audience or an SMS template. Was this intended? Your campaign will not be active if so.";
                    } else if (this.form["audience_id"] === "") {
                        this.modalText =
                            "You did not set an audience. Was this intended? Your campaign will not be active if so.";
                    } else if (this.form["sms_template_id"] === "") {
                        this.modalText =
                            "You did not set an SMS template. Was this intended? Your campaign will not be active if so.";
                    } else {
                        this.modalText =
                            "This form is not complete. Your campaign will not be active if you update.";
                    }
                }

                this.showConfirm = true;
            } else {
                this.modalText =
                    "This will undo your launch if you set one and shut off your drip. Are you sure?";
                this.showConfirm = true;
            }
        },
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
