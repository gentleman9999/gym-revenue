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
                    :value="true"
                />
                <label for="name" class="label ml-4"
                    >Activate (allows assigning to Campaigns)</label
                >
                <jet-input-error :message="form.errors.active" class="mt-2" />
            </div>

            <div
                class="form-control col-span-3 flex flex-col"
                v-if="form.active"
            >
                <p>Select an audience.</p>
                <select
                    v-if="audiences === undefined"
                    v-model="form.audience_id"
                    class="py-2"
                >
                    <option value="">No Audiences Available</option>
                </select>
                <select v-else v-model="form.audience_id" class="py-2">
                    <option value="">Avalaible Audiences</option>
                    <option
                        v-for="(audience, idy) in audiences"
                        :id="idy"
                        :value="audience.id"
                    >
                        {{ audience.name }}
                    </option>
                </select>
                <jet-input-error
                    :message="form.errors.audience_id"
                    class="mt-2"
                />
            </div>

            <div
                class="form-control col-span-3 flex flex-col"
                v-if="form.active"
            >
                <p>Select an SMS Template</p>
                <select
                    v-if="templates === undefined"
                    v-model="form.sms_template_id"
                    class="py-2"
                >
                    <option value="">No Templates Available</option>
                </select>
                <select v-else v-model="form.sms_template_id" class="py-2">
                    <option value="">Available Templates</option>
                    <option
                        v-for="(template, idx) in templates"
                        :id="idx"
                        :value="template.id"
                    >
                        {{ template.name }}
                    </option>
                </select>
                <jet-input-error
                    :message="form.errors.sms_template_id"
                    class="mt-2"
                />
            </div>

            <div
                class="form-control col-span-3 flex flex-col"
                v-if="form.active"
            >
                <p>Select a firing schedule</p>
                <select v-model="form.schedule" class="py-2">
                    <option value="drip">As Users are Added (Drip)</option>
                    <option value="bulk">All Subscribed Users (Bulk)</option>
                </select>
                <jet-input-error :message="form.errors.schedule" class="mt-2" />
            </div>

            <div
                class="form-control col-span-3 flex flex-col"
                v-if="form.active && form.schedule === 'bulk'"
            >
                <p>When should we trigger this email?</p>
                <select v-model="form.schedule_date" class="py-2">
                    <option value="now">Now</option>
                    <option value="1HOUR">1hr</option>
                </select>
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
    <sweet-modal
        v-if="buttonText === 'Update'"
        :title="modalText"
        width="85%"
        overlayTheme="dark"
        modal-theme="dark"
        enable-mobile-fullscreen
        ref="modal"
    >
        <Button
            class="btn-secondary"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            :loading="form.processing"
            type="button"
            @click="
                submitForm();
                closeModal();
            "
        >
            Yes
        </Button>
        <Button
            class="btn-warning"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            :loading="form.processing"
            type="button"
            @click="closeModal"
        >
            Nope
        </Button>
    </sweet-modal>
</template>

<script>
import { ref } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import SmsFormControl from "@/Components/SmsFormControl";
import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetInputError from "@/Jetstream/InputError";
import SweetModal from "@/Components/SweetModal3/SweetModal";

export default {
    name: "SmsCampaignForm",
    components: {
        AppLayout,
        Button,
        JetFormSection,
        SmsFormControl,
        JetInputError,
        SweetModal,
    },
    props: [
        "clientId",
        "campaign",
        "canActivate",
        "audiences",
        "templates",
        "audiences",
        "assignedTemplate",
        "assignedAudience",
    ],
    setup(props, context) {
        let modal = ref(null);
        let campaign = props.campaign;
        console.log("Campaign props", campaign);
        let operation = "Update";
        if (!campaign) {
            campaign = {
                name: null,
                active: false,
                audience_id: "",
                sms_template_id: "",
                schedule: "",
                schedule_date: "",
                // client_id: props.clientId
            };
            operation = "Create";
        } else {
            campaign["schedule_date"] = "now";
            campaign["schedule"] = "bulk";
            campaign["sms_template_id"] = props.assignedTemplate;
            campaign["audience_id"] = props.assignedAudience;
        }

        console.log("campaign Params", campaign);
        const form = useForm(campaign);

        let handleSubmit = () =>
            form.put(route("comms.sms-campaigns.update", campaign.id));
        if (operation === "Create") {
            handleSubmit = () => form.post(route("comms.sms-campaigns.store"));
        }

        return { form, buttonText: operation, handleSubmit, modal };
    },
    data() {
        return {
            modalText: "",
            activate: false,
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
                    if (this.form["schedule_date"] === "now") {
                        this.modalText =
                            "Are you sure you are ready to launch this Campaign? You won't be able to edit it afterwards.";
                    } else {
                        this.modalText =
                            "If you continue, you WILL be able to update or cancel this launch until the campaign time. Are you sure you want to do this?";
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
                            "You did not set an SMS Template. Was this intended? Your campaign will not be active if so.";
                    } else {
                        this.modalText =
                            "This form is not complete. Your campaign will not be active if you update. Are you okay with this?";
                    }
                }

                this.activate = ready;
                this.$refs.modal.open();
            } else {
                this.modalText =
                    "This will undo your launch if you set one and shut off your drip. Are you sure?";
                this.$refs.modal.open();
            }
        },
        submitForm(active) {
            this.form.active = this.activate;
            this.handleSubmit();
        },
        closeModal() {
            this.$refs.modal.close();
        },
    },
};
</script>

<style scoped></style>
