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
        return props.campaign === null
            ? createDripCampaign
            : updateDripCampaign;
    } else if (props.campaignType === "scheduled") {
        return props.campaign === null
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
        days.value =
            props.campaign === null
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
            date: false,
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

// const handleCancelScript = () => {
//     emit("update", days.value);
//     currentEditor.value = "default";
// };

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

// const saveCampaign = async () => {
//     if (invalidDays.value) {
//         return toastError(
//             "Each day must contain at least one method of communication"
//         );
//     }

//     if (
//         props?.campaignType === "scheduled" &&
//         days?.value?.length &&
//         !days?.value[0]?.date
//     ) {
//         return toastError("You must select a date to launch this campaign at.");
//     }

//     let fmtCampaign = {
//         name: props.name,
//         campaignType: props.campaignType,
//         audience_id: props.form.audience,
//         days: [
//             ...days.value.map((d) => {
//                 return {
//                     ...d,
//                     call:
//                         typeof d?.call?.message === "string"
//                             ? d.call.message
//                             : typeof d?.call === "string"
//                             ? d.call
//                             : null,
//                     email: typeof d?.email === "string" ? d.email : null,
//                     sms: typeof d?.sms === "string" ? d.sms : null,
//                     date:
//                         typeof d?.date !== undefined
//                             ? transformDate(d.date)
//                             : null,
//                 };
//             }),
//         ],
//     };
//     if (props.campaignType === "scheduled") {
//         fmtCampaign.send_at = fmtCampaign.days[0].date;
//         if (fmtCampaign.days[0]?.email) {
//             fmtCampaign.email_template_id = fmtCampaign.days[0].email;
//         }
//         if (fmtCampaign.days[0]?.sms) {
//             fmtCampaign.sms_template_id = fmtCampaign.days[0].sms;
//         }
//         if (fmtCampaign.days[0]?.call) {
//             fmtCampaign.call_template_id = fmtCampaign.days[0].call;
//         }
//         delete fmtCampaign.days;
//     }
//     console.log({ fmtCampaign, days: days.value });

//     try {
//         loading.value = true;
//         console.log("sending campaign", fmtCampaign);

//         /** route helpers */

//         let action = props.isNew ? "store" : "update";
//         let campType = props.campaignType === "drip" ? "drip" : "scheduled";
//         let endp = `mass-comms.${campType}-campaigns.${action}`;

//         console.log("sending to endpoint", endp);

//         const res = props.isNew
//             ? await axios.post(route(endp), {
//                   ...fmtCampaign,
//               })
//             : await axios.put(route(endp, props.existingId), {
//                   ...fmtCampaign,
//               });

//         if (res.status === 200) {
//             toastInfo("Campaign Saved!");
//             loading.value = false;
//             emit("done");
//         }
//         console.log("data received back from server:", res.data);
//     } catch (err) {
//         loading.value = false;
//         toastError("Problem Saving Campaign");
//         console.log("problem saving campaign", err);
//     }
// };
</script>
