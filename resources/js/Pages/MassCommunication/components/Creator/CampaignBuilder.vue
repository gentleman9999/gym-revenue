<template>
    <daisy-modal
        v-if="currentStep === 'audience-picker'"
        :open="true"
        :showCloseButton="false"
        :closable="false"
        class="h-full w-full bg-transparent border-none flex flex-col justify-center items-center shadow-none flex-grow"
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

                <AudienceSelect v-model="form.audience_id">
                    <template #label>
                        <div class="flex justify-between py-2 mt-4">
                            <label
                                class="text-black font-bold"
                                for="journey-audience"
                            >
                                Audience
                            </label>
                            <span class="text-black opacity-50">Required</span>
                        </div>
                    </template>
                </AudienceSelect>
                <button
                    @click="createAudience"
                    class="border border-secondary bg-secondary px-2 py-1 rounded-md hover:bg-base-content hover:text-secondary transition-all"
                >
                    New
                </button>

                <button
                    @click="() => (currentStep = 'audience-builder')"
                    :disabled="audiencePermissions[form.audience_id] === 0"
                    class="border border-secondary bg-secondary px-2 py-1 rounded-md hover:bg-base-content ml-4 hover:text-secondary transition-all disabled:opacity-20 disabled:hover:bg-secondary disabled:hover:text-base-content disabled:cursor-not-allowed"
                >
                    Edit
                </button>
            </div>
        </div>

        <div class="flex justify-between max-w-lg w-full mt-8 relative">
            <button
                @click="$emit('close')"
                class="selector-btn disabled:opacity-25 disabled:cursor-not-allowed bg-neutral hover:bg-neutral-content hover:bg-opacity-25 active:opacity-50"
            >
                Cancel
            </button>
            <button
                @click="handleAdvancementCheck"
                class="selector-btn bg-primary hover:bg-secondary active:opacity-50 disabled:opacity-25 disabled:cursor-not-allowed"
            >
                Next
            </button>
            <span></span>
        </div>
    </daisy-modal>

    <Scheduler
        v-if="currentStep === 'scheduler'"
        :isNew="campaign === null"
        :form="form"
        :name="form.name"
        :existingId="campaign?.id"
        :campaignType="type"
        :templates="form.templates"
        @back="cancelEditor"
        @update="handleUpdateTemplates"
        @done="handleDone"
    />

    <template v-if="currentStep === 'audience-builder'">
        <AudienceBuilder
            :audience_id="form.audience_id"
            :membership-types="membershipTypes"
            :lead-types="leadTypes"
            @cancel="cancelEditor"
            @update="handleAudienceUpdate"
        />
    </template>
</template>

<style scoped>
.selector-btn {
    @apply px-2 py-1 block transition-all rounded-md border border-transparent;
}
</style>

<script setup>
import queries from "@/gql/queries";
import { computed, ref, watch, onMounted } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import { toastError } from "@/utils/createToast";
import { useQuery } from "@vue/apollo-composable";
import { Inertia } from "@inertiajs/inertia";
import {
    transformAudience,
    transformDayTemplate,
    transformSource,
} from "./helpers";

import DaisyModal from "@/Components/DaisyModal.vue";
import AudienceBuilder from "../AudienceBuilder/AudienceBuilder.vue";
import AudienceSelect from "../AudienceBuilder/AudienceSelect.vue";
import Scheduler from "../Scheduler.vue";

const props = defineProps({
    type: {
        type: String,
        default: "scheduled",
    },
    campaign: {
        type: [Object, null],
        default: null,
    },
});

const editParam = ref(
    props.campaign === null ? null : { id: props.campaign?.id }
);

const form = ref({ ...props.campaign });

const currentStep = ref("audience-picker");
const audiencePermissions = ref({});

const {
    result,
    loading: apermLoading,
    error,
    refetch,
} = useQuery(queries["audiencePermissions"]);

watch(result, (data) => {
    console.log("audience permissions", data);
    if (!!data["audiences"]) {
        data["audiences"]?.data.forEach((aud) => {
            audiencePermissions.value[aud.id] = aud["editable"];
        });
    }
});

const emit = defineEmits(["close"]);

// const defaultTemplatesDrip = [
//     {
//         email: false,
//         sms: false,
//         call: false,
//         date: false,
//         day_in_campaign: 0,
//     },
// ];

// const defaultTemplatesScheduled = [
//     {
//         email: false,
//         sms: false,
//         call: false,
//         date: null,
//         day_in_campaign: 0,
//     },
// ];

/** @TODO gotta get rid of these... need a way to get member/lead types from gql */
/************************************************************/
const membershipTypes = computed(() =>
    transformSource(usePage().props.value.member_types)
);
const leadTypes = computed(() =>
    transformSource(usePage().props.value.lead_types)
);
/************************************************************/

/**
 * check for invalid entries and return an informative message
 * to the user to fix it before submission
 */
const advancementDisabled = computed(() => {
    if (typeof form.value.audience_id !== "string")
        return "You must choose an audience.";
    if (!form.value.name || form.value.name.trim() === "")
        return "You must name your campaign.";
    return false;
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
// const updateAudiences = (newAudience) => {
//     currentStep.value = "audience-picker";
// };

const handleUpdateTemplates = (templates) => {
    form.value.templates = templates;
};

/** New audience creation */
const createAudience = () => {
    form.value.audience_id = "";
    currentStep.value = "audience-builder";
};

// /** Modify existing audiences */
// const editAudience = (id) => {
//     form.value.audience_id = id;
//     currentStep.value = "audience-builder";
// };

const handleAudienceUpdate = (audience) => {
    currentStep.value = "audience-picker";
    form.value.audience_id = audience?.id ?? "";
};

const cancelEditor = () => {
    currentStep.value = "audience-picker";
};

const handleDone = () => {
    emit("close");
};
</script>
