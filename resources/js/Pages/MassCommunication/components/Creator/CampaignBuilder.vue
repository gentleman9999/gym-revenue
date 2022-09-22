<template>
    <daisy-modal
        v-if="currentStep === 'audience-picker'"
        :open="true"
        :showCloseButton="false"
        :closable="false"
        class="h-full w-full bg-transparent border-none flex flex-col justify-center items-center shadow-none"
    >
        <div class="text-center">
            <h1 class="text-2xl">Create a map of your contact's journey</h1>
            <p class="py-4">
                Put your contacts on a path that's right for them. With a custom
                journey, you can <br />
                always be there for your contacts when they need you most.
            </p>
        </div>

        <div
            class="bg-black border border-secondary rounded-lg p-4 mt-4 max-w-lg w-full"
        >
            <div class="border border-secondary bg-base-content rounded-md p-4">
                <div class="flex justify-between py-2">
                    <label class="text-black font-bold" for="journey-name">
                        Name
                    </label>
                    <span class="text-black opacity-50">Required</span>
                </div>
                <input
                    v-model="form.name"
                    placeholder="New Campaign"
                    class="bg-base-content text-black p-2 rounded-none border border-black w-full placeholder-black"
                    type="text"
                    id="journey-name"
                />

                <div class="flex justify-between py-2 mt-4">
                    <label class="text-black font-bold" for="journey-audience">
                        Audience
                    </label>
                    <span class="text-black opacity-50">Required</span>
                </div>
                <select
                    v-model="form.audience"
                    id="journey-audience"
                    class="bg-base-content text-black p-2 rounded-none border border-black w-full mb-4"
                >
                    <option :disabled="true" :value="null" :selected="true">
                        Select an Audience
                    </option>
                    <option
                        v-for="audience in propAudiences"
                        :disabled="audience.title === 'Select an Audience'"
                        :value="audience.id"
                        :selected="audience.id === form.audience"
                    >
                        {{ audience.title }}
                    </option>
                </select>

                <button
                    @click="createAudience"
                    class="border border-secondary bg-secondary px-2 py-1 rounded-md hover:bg-base-content hover:text-secondary transition-all"
                >
                    New
                </button>

                <button
                    @click="() => editAudience(selectedAudience.id)"
                    :disabled="!selectedAudience?.editable"
                    class="border border-secondary bg-secondary px-2 py-1 rounded-md hover:bg-base-content ml-4 hover:text-secondary transition-all disabled:opacity-20 disabled:hover:bg-secondary disabled:hover:text-base-content disabled:cursor-not-allowed"
                >
                    Edit
                </button>
            </div>
        </div>

        <div class="flex justify-between max-w-lg w-full mt-8 relative">
            <button @click="$emit('close')" class="">Back</button>
            <button
                @click="handleAdvancementCheck"
                class="border border-secondary px-2 py-1 rounded-md hover:bg-secondary transition-all disabled:opacity-25 disabled:cursor-not-allowed"
            >
                Next
            </button>
            <span></span>
        </div>
    </daisy-modal>

    <Scheduler
        v-if="currentStep === 'scheduler'"
        :isNew="precampaign === null"
        :form="form"
        :name="form.name"
        :existingId="precampaign?.id"
        :campaignType="campaignType"
        :templates="form.templates"
        :topol-api-key="topolApiKey"
        :email_templates="emailTemplates"
        :sms_templates="smsTemplates"
        :call_templates="callTemplates"
        :temp_audiences="propAudiences"
        @back="
            () => {
                currentStep = 'audience-picker';
            }
        "
        @update="
            (v) => {
                form.templates = v;
            }
        "
        @done="handleDone"
    />

    <AudienceBuilder
        v-if="currentStep === 'audience-builder'"
        :endpoint="routeEndpoint"
        :audience="tempAudience"
        :membership-types="membershipTypes"
        :lead-types="leadTypes"
        @canceled="cancelEditor"
        @save="updateAudiences"
    />
</template>

<script setup>
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import { toastError } from "@/utils/createToast";
import { Inertia } from "@inertiajs/inertia";
import {
    audienceItemTemplate,
    transformAudience,
    transformDayTemplate,
    transformSource,
} from "./helpers";
import DaisyModal from "@/Components/DaisyModal.vue";
import AudienceBuilder from "../AudienceBuilder/AudienceBuilder.vue";
import Scheduler from "../Scheduler.vue";

const props = defineProps({
    campaignType: {
        type: String,
        required: true,
    },
    topolApiKey: {
        type: String,
        required: true,
    },
    precampaign: {
        type: [Object, null],
        default: null,
    },
});

const emit = defineEmits(["done"]);

const defaultTemplatesDrip = [
    {
        email: false,
        sms: false,
        call: false,
        date: false,
        day_in_campaign: 0,
    },
];

const defaultTemplatesScheduled = [
    {
        email: false,
        sms: false,
        call: false,
        date: null,
        day_in_campaign: 0,
    },
];

const form = ref({
    name: props?.precampaign?.name ? props.precampaign.name : null,
    audience: props?.precampaign?.audience_id
        ? props.precampaign.audience_id
        : null,
    templates:
        props.campaignType === "drip"
            ? props?.precampaign?.days?.map((d) => transformDayTemplate(d)) ||
              defaultTemplatesDrip
            : props?.precampaign
            ? [transformDayTemplate(props.precampaign)]
            : defaultTemplatesScheduled,
});

const membershipTypes = computed(() =>
    transformSource(usePage().props.value.member_types)
);
const leadTypes = computed(() =>
    transformSource(usePage().props.value.lead_types)
);
const propAudiences = computed(() =>
    transformAudience(usePage().props.value.audiences)
);
const emailTemplates = computed(() => {
    return usePage().props.value.email_templates;
});
const smsTemplates = computed(() => {
    return usePage().props.value.sms_templates;
});
const callTemplates = computed(() => {
    return usePage().props.value.call_templates;
});

const currentStep = ref("audience-picker");
const tempAudience = ref(null);

/**
 * check for invalid entries and return an informative message
 * to the user to fix it before submission
 */
const advancementDisabled = computed(() => {
    if (form.value.audience === "off-default")
        return "You must choose an audience.";
    if (!form.value.name || form.value.name.trim() === "")
        return "You must name your campaign.";
    return false;
});

/** audience endpoints to update an existing or create a new one if necessary */
const routeEndpoint = computed(() => {
    return propAudiences.value.filter((a) => a?.id === tempAudience.value.id)
        .length > 0
        ? "mass-comms.audiences.update"
        : "mass-comms.audiences.create";
});

const selectedAudience = computed(() => {
    return propAudiences.value.filter((v) => v?.id === form.value.audience)[0];
});

/**
 * check if data is sufficient for advancement
 * if not, inform them of what needs to be changed or advance if sufficient
 */
const handleAdvancementCheck = () => {
    if (typeof advancementDisabled.value !== "string")
        return (currentStep.value = "scheduler");

    toastError(advancementDisabled.value);
};

/**
 * handles the 'save' emit from the AudienceBuilder.
 * replace the existing audience or add it new if it doesn't exist.
 */
const updateAudiences = (newAudience) => {
    let fmtAudience = audienceItemTemplate(newAudience);

    Inertia.reload({ only: ["audiences"] });

    form.value.audience = fmtAudience?.id;
    tempAudience.value = null;
    currentStep.value = "audience-picker";
};

/**
 * temporarily creates a new audience with default values that can be freely modified,
 * if it isn't saved we can simply destroy it or add it to the existing audiences if it is.
 */
const createAudience = () => {
    tempAudience.value = audienceItemTemplate({
        title: "New Audience",
        filters: [],
    });

    currentStep.value = "audience-builder";
};

/**
 * places a copy of the existing audience into a temporary one that can be freely modified.
 * we only need to update back end if the audience is actually saved
 */
const editAudience = (id) => {
    tempAudience.value = propAudiences.value.filter((a) => a.id === id)[0];
    currentStep.value = "audience-builder";
};

const cancelEditor = () => {
    tempAudience.value = null;
    currentStep.value = "audience-picker";
};

const handleDone = () => {
    emit("done");
};
</script>
