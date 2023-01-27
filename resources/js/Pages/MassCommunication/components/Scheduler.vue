<template>
    <Modal>
        <div
            class="flex flex-col h-full w-full justify-center items-center bg-black bg-opacity-80"
        >
            <div
                v-if="currentEditor === 'default'"
                class="bg-black p-8 border-secondary border rounded-md max-w-5xl w-full"
                :class="{
                    'max-w-sm text-center': campaignType === 'scheduled',
                }"
            >
                <h2 class="font-bold py-2 text-lg">
                    {{
                        campaignType === "drip"
                            ? "Build your drip campaign by day"
                            : "Build your scheduled campaign"
                    }}
                </h2>

                <div
                    v-if="campaignType === 'scheduled' && mountingFinished"
                    class="border border-secondary rounded-md py-4 px-2"
                >
                    <Day
                        :index="0"
                        :all_days="days"
                        :mailActive="days[0].email_template_id"
                        :smsActive="days[0].sms_template_id"
                        :phoneActive="days[0].call_template_id"
                        :date="days[0].send_at"
                        @email="() => updateEditor(0, 'email')"
                        @phone="() => updateEditor(0, 'call')"
                        @sms="() => updateEditor(0, 'sms')"
                        @reset-email="() => resetField(0, 'email_template_id')"
                        @reset-sms="() => resetField(0, 'sms_template_id')"
                        @reset-call="() => resetField(0, 'call_template_id')"
                        @date="(t) => handleSave('send_at', t)"
                        :multiday="false"
                    />
                </div>

                <div
                    v-if="campaignType === 'drip' && mountingFinished"
                    class="border border-secondary rounded-md py-4 px-2 overflow-x-scroll overflow-y-hidden flex"
                >
                    <Day
                        v-for="(day, ix) in days"
                        :all_days="days"
                        :day_ix="day.day_of_campaign"
                        :index="ix"
                        :mailActive="day.email_template_id"
                        :smsActive="day.sms_template_id"
                        :phoneActive="day.call_template_id"
                        @email="() => updateEditor(ix, 'email')"
                        @phone="() => updateEditor(ix, 'call')"
                        @sms="() => updateEditor(ix, 'sms')"
                        @day_ix="(ind) => updateDayIx(ix, ind)"
                        @reset-email="(i) => resetField(i, 'email_template_id')"
                        @reset-sms="(i) => resetField(i, 'sms_template_id')"
                        @reset-call="(i) => resetField(i, 'call_template_id')"
                        :multiday="true"
                    />

                    <div class="flex items-center">
                        <div class="w-8 h-[0.125rem] bg-base-content mx-2" />
                        <button
                            @click="addDay"
                            class="bg-secondary rounded-full h-8 w-8"
                        >
                            <font-awesome-icon icon="plus" />
                        </button>
                    </div>
                </div>
                <div v-if="!mountingFinished">
                    <Spinner />
                </div>

                <div class="flex justify-between mt-8">
                    <button
                        :disabled="loading"
                        @click="$emit('back')"
                        class="selector-btn disabled:opacity-25 disabled:cursor-not-allowed bg-neutral hover:bg-neutral-content hover:bg-opacity-25 active:opacity-50"
                    >
                        Back
                    </button>

                    <button
                        :disabled="loading"
                        @click="saveCampaign"
                        class="selector-btn bg-primary hover:bg-secondary active:opacity-50 disabled:opacity-25 disabled:cursor-not-allowed"
                    >
                        Save Campaign
                    </button>
                    <span></span>
                </div>
            </div>

            <Templates
                v-if="currentEditor === 'call'"
                @cancel="currentEditor = 'default'"
                @save="(template) => handleSave('call_template_id', template)"
                :selected="days[currentDayIndex].call_template_id"
                template_type="call"
            />

            <Templates
                v-if="currentEditor === 'email'"
                @cancel="currentEditor = 'default'"
                @save="(template) => handleSave('email_template_id', template)"
                :selected="days[currentDayIndex].email_template_id"
                template_type="email"
            />

            <Templates
                v-if="currentEditor === 'sms'"
                @cancel="currentEditor = 'default'"
                @save="(template) => handleSave('sms_template_id', template)"
                :selected="days[currentDayIndex].sms_template_id"
                template_type="sms"
            />
        </div>
    </Modal>
</template>

<style scoped>
.selector-btn {
    @apply px-2 py-1 block transition-all rounded-md border border-transparent;
}
</style>

<script setup>
import * as _ from "lodash";
import { ref, computed, onMounted } from "vue";
import { faPlus } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { transformDate } from "@/utils/transformDate";
import { toastInfo, toastError } from "@/utils/createToast";

import Modal from "@/Components/ModalUnopinionated.vue";
import DaisyModal from "@/Components/DaisyModal.vue";
import Day from "./Creator/Day.vue";
import Templates from "./Templates.vue";
import Spinner from "@/Components/Spinner.vue";
import mutations from "@/gql/mutations";
import { useMutation } from "@vue/apollo-composable";

library.add(faPlus);

const props = defineProps({
    campaign: {
        type: [Object, null],
        default: null,
    },
    campaignType: {
        type: String,
        required: true,
    },
});

const dayShape = {
    email_template_id: false,
    sms_template_id: false,
    call_template_id: false,
    send_at: false,
    day_in_campaign: 0,
};

const mountingFinished = ref(false);

const emit = defineEmits(["back", "update", "done"]);

const { mutate: createDripCampaign } = useMutation(
    mutations.dripCampaign.create
);
const { mutate: updateDripCampaign } = useMutation(
    mutations.dripCampaign.update
);
const { mutate: createScheduledCampaign } = useMutation(
    mutations.scheduledCampaign.update
);
const { mutate: updateScheduledCampaign } = useMutation(
    mutations.scheduledCampaign.update
);

const operFn = computed(() => {
    if (props.campaignType === "drip") {
        return !props.campaign?.id ? createDripCampaign : updateDripCampaign;
    } else if (props.campaignType === "scheduled") {
        return !props.campaign?.id
            ? createScheduledCampaign
            : updateScheduledCampaign;
    }
});

const currentDayIndex = ref(0);
const currentEditor = ref("default");
const loading = ref(false);

const days = ref([
    {
        email_template_id: "",
        sms_template_id: "",
        call_template_id: "",
        send_at: "",
        day_in_campaign: "",
    },
]);

onMounted(() => {
    if (props.campaignType === "drip") {
        days.value = !props.campaign?.id
            ? [{ ...dayShape }]
            : _.cloneDeep(props.campaign.days);
    } else {
        days.value = [
            {
                email_template_id: props?.campaign?.email_template_id ?? false,
                call_template_id: props?.campaign?.call_template_id ?? false,
                sms_template_id: props?.campaign?.sms_template_id ?? false,
                send_at: props?.campaign?.send_at ?? false,
            },
        ];
    }

    mountingFinished.value = true;
});

const addDay = () => {
    let maxDay =
        Math.max(...[...days.value].map((d) => d?.day_in_campaign)) ?? 0;

    days.value = [
        ...days.value,
        {
            email_template_id: false,
            sms_template_id: false,
            call_template_id: false,
            day_in_campaign: maxDay + 1,
        },
    ];
};

/** Determines an invalid campaign via the absence of all three types of communication methods on a single day */
const campaignHasInvalidDays = computed(() => {
    let invalid = false;

    for (const day of days.value) {
        if (!day.call && !day.email && !day.sms) invalid = true;
    }

    return invalid;
});

const resetField = (i, field) => {
    days.value[i][field] = false;
};

const updateEditor = (i, method) => {
    currentDayIndex.value = i;
    currentEditor.value = method;
};

const handleSave = (method, val) => {
    days.value[currentDayIndex.value][method] = val;
    emit("update", days.value);
    currentEditor.value = "default";
};

const updateDayIx = (ix, v) => {
    days.value[ix].day_in_campaign = v;
};

const saveCampaign = async () => {
    let dripDays = _.cloneDeep(days.value);

    for (const dripDay of dripDays) {
        if (dripDay?.send_at === 0 || dripDay?.send_at === false)
            delete dripDay["send_at"];
        if (dripDay?.email_template_id === false)
            delete dripDay["email_template_id"];
        if (dripDay?.call_template_id === false)
            delete dripDay["call_template_id"];
        if (dripDay?.sms_template_id === false)
            delete dripDay["sms_template_id"];
    }

    let inputDataDrip = {
        name: props.campaign.name,
        campaignType: props.campaignType,
        audience_id: props.campaign.audience_id,
        days: dripDays,
    };

    let inputDataScheduled = {
        name: props.campaign.name,
        campaignType: props.campaignType,
        audience_id: props.campaign.audience_id,
        email_template_id: days.value[0].email_template_id,
        call_template_id: days.value[0].call_template_id,
        sms_template_id: days.value[0].smsl_template_id,
        send_at: days.value[0].send_at,
    };

    let inputData =
        props.campaignType === "drip" ? inputDataDrip : inputDataScheduled;

    try {
        await operFn.value({
            campaign: inputData,
        });
    } catch (error) {
        toastError("Problem saving campaign");
    }
};
</script>
