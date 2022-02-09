<template>
    <jet-form-section @submitted="handleSubmit">
        <template #form>
            <div class="form-control col-span-6">
                <label for="name" class="label">Name</label>
                <input type="text" v-model="form.name" autofocus id="name"/>
                <jet-input-error :message="form.errors.name" class="mt-2"/>
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
                <jet-input-error :message="form.errors.active" class="mt-2" v-model="form.active"/>
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
                <p>Select an Email Template</p>
                <multiselect
                    v-model="form.email_templates"
                    class="py-2"
                    :disabled="!canEditActiveInputs"
                    id="email_templates"
                    mode="tags"
                    :close-on-select="false"
                    :create-option="true"
                    :options="
                         availableEmailTemplates.map((email_templates) => ({
                            label: email_templates.name,
                            value: email_templates.id,
                        }))
                    "
                    :classes="multiselectClasses"
                />
                <jet-input-error
                    :message="form.errors.email_templates"
                    class="mt-2"
                />
            </div>

            <div
                class="form-control col-span-3 flex flex-col"
                v-if="form.active"
            >
                <p>Select a firing schedule</p>
                <select v-model="form.schedule" class="py-2" :disabled="!canEditActiveInputs">
                    <option value="">Available Schedules</option>
                    <option value="drip">As Users are Added (Drip)</option>
                    <option value="bulk">All Subscribed Users (Bulk)</option>
                </select>
                <jet-input-error :message="form.errors.schedule" class="mt-2"/>
            </div>

            <div
                class="form-control col-span-3 flex flex-col"
                v-if="form.active && form.schedule === 'bulk'"
            >
                <p>When should we trigger this email?</p>
                <div class="flex flex-row gap-8 h-16"><label class="label">
                    <span class="label-text mr-2">Now</span>
                    <input type="radio" name="scheduleNow" checked="checked" class="radio" v-model="scheduleNow" :disabled="!canEditActiveInputs">
                </label>
                    <label class="label">
                        <span class="label-text mr-2">Later</span>
                        <input type="radio" name="scheduleNow" :value="false" class="radio" v-model="scheduleNow" :disabled="!canEditActiveInputs">
                    </label>
                </div>
                <date-picker v-model="form.schedule_date" dark :disabled="!canEditActiveInputs" v-if="!scheduleNow"
                             :min-date=" new Date((new Date()).valueOf() - 1000*60*60*24)"/>
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
                @click="$inertia.visit(route('comms.email-campaigns'))"
                :class="{ 'opacity-25': form.processing }"
                error
                outline
                :disabled="form.processing"
            >
                Cancel
            </Button>
            <div class="flex-grow"/>
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
        @confirm="submitForm();showConfirm=false;"
        @cancel="showConfirm=false;"
    >
        {{ modalText }}
    </confirm>
</template>

<script>
import {computed, ref} from "vue";
import {useForm, usePage} from "@inertiajs/inertia-vue3";
import SmsFormControl from "@/Components/SmsFormControl";
import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetInputError from "@/Jetstream/InputError";
import Confirm from "@/Components/Confirm";
import DatePicker from 'vue3-date-time-picker';
import 'vue3-date-time-picker/dist/main.css'
import Multiselect from "@vueform/multiselect";

export default {
    name: "EmailCampaignForm",
    components: {
        AppLayout,
        Button,
        JetFormSection,
        SmsFormControl,
        JetInputError,
        Confirm,
        DatePicker,
        Multiselect
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
        const scheduleNow = ref(isNaN(Date.parse(props.campaign?.schedule_date?.value)));
        let campaign = props.campaign;
        let operation = "Update";
        if (!campaign) {
            campaign = {
                name: null,
                active: false,
                audiences: [],
                email_templates: [],
                schedule: "",
                schedule_date: "",
                // client_id: props.clientId
            };
            operation = "Create";
        } else {
            campaign["schedule_date"] = campaign.schedule_date?.value || 'now';
            campaign["schedule"] = campaign.schedule?.value || '';

            campaign["email_templates"] = page.props.value.emailTemplates.map(template_id=>template_id.value);
            campaign["audiences"] = page.props.value.audiences.map(audience_id=>audience_id.value);
        }

        console.log("campaign Params", campaign);
        const form = useForm(campaign);

        let handleSubmit = () => {
            form.transform(data=> ({...data, schedule_date: scheduleNow.value ? 'now' : data.schedule_date})).put(route("comms.email-campaigns.update", campaign.id));
        };
        if (operation === "Create") {
            handleSubmit = () =>
                form.post(route("comms.email-campaigns.store"));
        }

        // const canEditActiveInputs = !props.campaign?.schedule_date;
        // console.log({canEditActiveInputs: canEditActiveInputs});
        // const canEditActiveInputs = computed(() => !props.campaign.active && (!props.campaign?.schedule_date || new Date() < new Date(props.campaign.schedule_date)));
        const canEditActiveInputs = computed(() => {
            if (!props.campaign?.active) {
                return true;
            }
            return !props.campaign?.schedule_date || new Date() < new Date(`${props.campaign.schedule_date} UTC`);
        });
        console.log({canEditActiveInputs: canEditActiveInputs.value});

        const multiselectClasses = {
            container:
                "relative mx-auto w-full flex items-center justify-end box-border cursor-pointer border border-2 border-base-content border-opacity-20 rounded-lg bg-base-100 text-base leading-snug outline-none min-h-12",
            containerDisabled: "cursor-default bg-base-200",
            containerOpen: "rounded-b-none",
            containerOpenTop: "rounded-t-none",
            containerActive: "ring ring-primary",
            singleLabel:
                "flex items-center h-full max-w-full absolute left-0 top-0 pointer-events-none bg-transparent leading-snug pl-3.5 pr-16 box-border",
            singleLabelText:
                "overflow-ellipsis overflow-hidden block whitespace-nowrap max-w-full",
            multipleLabel:
                "flex items-center h-full absolute left-0 top-0 pointer-events-none bg-transparent leading-snug pl-3.5",
            search: "w-full absolute inset-0 outline-none focus:ring-0 appearance-none box-border border-0 text-base font-sans bg-base-100 rounded pl-3.5",
            tags: "flex-grow flex-shrink flex flex-wrap items-center mt-1 pl-2",
            tag: "bg-primary text-base-content text-sm font-semibold py-0.5 pl-2 rounded mr-1 mb-1 flex items-center whitespace-nowrap",
            tagDisabled: "pr-2 opacity-50",
            tagRemove:
                "flex items-center justify-center p-1 mx-0.5 rounded-sm hover:bg-black hover:bg-opacity-10 group",
            tagRemoveIcon:
                "bg-multiselect-remove text-base-con bg-center bg-no-repeat opacity-30 inline-block w-3 h-3 group-hover:opacity-60",
            tagsSearchWrapper:
                "inline-block relative mx-1 mb-1 flex-grow flex-shrink h-full",
            tagsSearch:
                "absolute inset-0 border-0 outline-none focus:ring-0 appearance-none p-0 text-base font-sans box-border w-full",
            tagsSearchCopy: "invisible whitespace-pre-wrap inline-block h-px",
            placeholder:
                "flex items-center h-full absolute left-0 top-0 pointer-events-none bg-transparent leading-snug pl-3.5 text-base-content text-opacity-50",
            caret: "bg-multiselect-caret bg-center bg-no-repeat w-2.5 h-4 py-px box-content mr-3.5 relative z-10 opacity-40 flex-shrink-0 flex-grow-0 transition-transform transform pointer-events-none",
            caretOpen: "rotate-180 pointer-events-auto",
            clear: "pr-3.5 relative z-10 opacity-40 transition duration-300 flex-shrink-0 flex-grow-0 flex hover:opacity-80",
            clearIcon:
                "bg-multiselect-remove bg-center bg-no-repeat w-2.5 h-4 py-px box-content inline-block",
            spinner:
                "bg-multiselect-spinner bg-center bg-no-repeat w-4 h-4 z-10 mr-3.5 animate-spin flex-shrink-0 flex-grow-0",
            dropdown:
                "max-h-60 absolute -left-px -right-px bottom-0 transform translate-y-full border border-gray-300 -mt-px overflow-y-scroll z-50 bg-base-100 flex flex-col rounded-b",
            dropdownTop:
                "-translate-y-full top-px bottom-auto flex-col-reverse rounded-b-none rounded-t",
            dropdownHidden: "hidden",
            options: "flex flex-col p-0 m-0 list-none",
            optionsTop: "flex-col-reverse",
            group: "p-0 m-0",
            groupLabel:
                "flex text-sm box-border items-center justify-start text-left py-1 px-3 font-semibold bg-base-300 cursor-default leading-normal",
            groupLabelPointable: "cursor-pointer",
            groupLabelPointed: "bg-base-300 text-base-content text-opacity-70",
            groupLabelSelected: "bg-green-600 text-base-content",
            groupLabelDisabled:
                "bg-base-200 text-base-content text-opacity-50 cursor-not-allowed",
            groupLabelSelectedPointed:
                "bg-green-600 text-base-content opacity-90",
            groupLabelSelectedDisabled:
                "text-green-100 bg-green-600 bg-opacity-50 cursor-not-allowed",
            groupOptions: "p-0 m-0",
            option: "flex items-center justify-start box-border text-left cursor-pointer text-base leading-snug py-2 px-3",
            optionPointed: "bg-primary",
            optionSelected: "text-base-content bg-green-500",
            optionDisabled:
                "text-base-content text-opacity-50 cursor-not-allowed",
            optionSelectedPointed: "text-base-content bg-green-500 opacity-90",
            optionSelectedDisabled:
                "text-green-100 bg-green-500 bg-opacity-50 cursor-not-allowed",
            noOptions:
                "py-2 px-3 text-base-content text-opacity-50 bg-base-100 text-left",
            noResults:
                "py-2 px-3 text-base-content text-opacity-50 bg-base-100 text-left",
            fakeInput:
                "bg-transparent absolute left-0 right-0 -bottom-px w-full h-px border-0 p-0 appearance-none outline-none text-transparent",
            spacer: "h-9 py-px box-content",
        };

        return {form, buttonText: operation, handleSubmit, modal, canEditActiveInputs, scheduleNow, availableAudiences: page.props.value.availableAudiences, availableEmailTemplates: page.props.value.availableEmailTemplates ,multiselectClasses};
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
                    this.form["email_template_id"] !== "";

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
                        this.form["email_template_id"] === ""
                    ) {
                        this.modalText =
                            "You did not set an audience or an email template. Was this intended? Your campaign will not be active if so.";
                    } else if (this.form["audience_id"] === "") {
                        this.modalText =
                            "You did not set an audience. Was this intended? Your campaign will not be active if so.";
                    } else if (this.form["email_template_id"] === "") {
                        this.modalText =
                            "You did not set an email template. Was this intended? Your campaign will not be active if so.";
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
