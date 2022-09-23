<template>
    <daisy-modal
        :open="true"
        :showCloseButton="false"
        :closable="false"
        class="h-full w-full bg-transparent border-none flex flex-col justify-center items-center shadow-none"
    >
        <div
            v-if="currentEditor === 'default'"
            class="bg-black p-8 border-secondary border rounded-md max-w-5xl w-full"
            :class="{ 'max-w-sm text-center': campaignType === 'scheduled' }"
        >
            <h2 class="font-bold py-2 text-lg">
                {{
                    campaignType === "drip"
                        ? "Build your drip campaign by day"
                        : "Build your scheduled campaign"
                }}
            </h2>
            <div
                v-if="campaignType === 'scheduled'"
                class="border border-secondary rounded-md py-4 px-2"
            >
                <Day
                    :index="0"
                    :mailActive="days[0].email"
                    :smsActive="days[0].sms"
                    :phoneActive="days[0].call"
                    :date="days[0].date"
                    @email="() => updateEditor(0, 'email')"
                    @phone="() => updateEditor(0, 'call')"
                    @sms="() => updateEditor(0, 'sms')"
                    @reset-email="() => resetField(0, 'email')"
                    @reset-sms="() => resetField(0, 'sms')"
                    @reset-call="() => resetField(0, 'call')"
                    @date="(t) => handleSave('date', t)"
                    :multiday="false"
                />
            </div>

            <div
                v-if="campaignType === 'drip'"
                class="border border-secondary rounded-md py-4 px-2 overflow-x-scroll overflow-y-hidden flex"
            >
                <Day
                    v-for="(day, ix) in days"
                    :all_days="days"
                    :day_ix="day.day_in_campaign"
                    :index="ix"
                    :mailActive="day.email"
                    :smsActive="day.sms"
                    :phoneActive="day.call"
                    @email="() => updateEditor(ix, 'email')"
                    @phone="() => updateEditor(ix, 'call')"
                    @sms="() => updateEditor(ix, 'sms')"
                    @day_ix="(ind) => updateDayIx(ix, ind)"
                    @reset-email="(i) => resetField(i, 'email')"
                    @reset-sms="(i) => resetField(i, 'sms')"
                    @reset-call="(i) => resetField(i, 'call')"
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
            <div class="flex justify-between mt-8">
                <button
                    :disabled="loading"
                    @click="$emit('back')"
                    class="disabled:opacity-25 disabled:cursor-not-allowed"
                >
                    Back
                </button>

                <button
                    :disabled="loading"
                    @click="saveCampaign"
                    class="text-base-content disabled:opacity-25 disabled:cursor-not-allowed rounded-md px-2 py-1 text-base bg-secondary"
                >
                    Save Campaign
                </button>
                <span></span>
            </div>
        </div>

        <!-- <CallScript
            v-if="currentEditor === 'call'"
            @cancel="handleCancelScript"
            @save="(script) => handleSave('call', script)"
            :message="days[currentDayIndex]?.call?.message"
        /> -->

        <Templates
            v-if="currentEditor === 'call'"
            @cancel="handleCancelScript"
            @save="(template) => handleSave('call', template)"
            :selected="days[currentDayIndex].call"
            :templates="call_templates"
            :topol-api-key="topolApiKey"
            :template_type="'call'"
        />

        <Templates
            v-if="currentEditor === 'email'"
            @close="handleCancelScript"
            @save="(template) => handleSave('email', template)"
            :selected="days[currentDayIndex].email"
            :templates="email_templates"
            :topol-api-key="topolApiKey"
            :template_type="'email'"
        />

        <Templates
            v-if="currentEditor === 'sms'"
            @close="handleCancelScript"
            @save="(template) => handleSave('sms', template)"
            :selected="days[currentDayIndex].sms"
            :templates="sms_templates"
            :topol-api-key="topolApiKey"
            :template_type="'sms'"
        />
    </daisy-modal>
</template>

<script setup>
import { ref, computed } from "vue";
import { faPlus } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { transformDate } from "@/utils/transformDate";
import { transformDayTemplate } from "./Creator/helpers";
import { toastInfo, toastError } from "@/utils/createToast";
import axios from "axios";

import DaisyModal from "@/Components/DaisyModal.vue";

import Day from "./Creator/Day.vue";
import CallScript from "./Creator/CallScript.vue";
import Templates from "./Templates.vue";
library.add(faPlus);

const props = defineProps({
    name: {
        type: String,
        default: "Test Name",
    },
    isNew: {
        type: Boolean,
        default: true,
    },
    existingId: {
        type: [String, null, undefined],
        default: null,
    },
    campaignType: {
        type: String,
        required: true,
    },
    email_templates: {
        type: Array,
        default: [],
    },
    sms_templates: {
        type: Array,
        default: [],
    },
    call_templates: {
        type: Array,
        default: [],
    },
    templates: {
        type: Array,
        default: [
            {
                email: false,
                sms: false,
                call: false,
                date: false,
                day_in_campaign: 0,
            },
        ],
    },
    form: {
        type: Object,
        default: {},
    },
    topolApiKey: {
        type: String,
        required: true,
    },
    temp_audiences: {
        type: Array,
        default: [],
    },
});

const emit = defineEmits(["back", "update", "done"]);

const currentDayIndex = ref(0);
const currentEditor = ref("default");
const loading = ref(false);

const days = ref(props.templates);

const addDay = () => {
    let daysArr = [...days.value];

    let extractDays = daysArr.map((d) =>
        typeof d?.day_in_campaign === "number" ? d.day_in_campaign : 0
    );

    let highestDayIx = Math.max(...extractDays);

    days.value = [
        ...days.value,
        {
            email: false,
            sms: false,
            call: false,
            date: false,
            day_in_campaign: highestDayIx + 1,
        },
    ];
};

const invalidDays = computed(() => {
    let isInvalid = false;

    for (const d of days.value) {
        if (!d.call && !d.email && !d.sms) isInvalid = true;
    }

    return isInvalid;
});

const resetField = (i, field) => {
    days.value[i][field] = false;
};

const handleCancelScript = () => {
    emit("update", days.value);
    currentEditor.value = "default";
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
    if (invalidDays.value) {
        return toastError(
            "Each day must contain at least one method of communication"
        );
    }

    if (
        props?.campaignType === "scheduled" &&
        days?.value?.length &&
        !days?.value[0]?.date
    ) {
        return toastError("You must select a date to launch this campaign at.");
    }

    let fmtCampaign = {
        name: props.name,
        campaignType: props.campaignType,
        audience_id: props.form.audience,
        days: [
            ...days.value.map((d) => {
                return {
                    ...d,
                    call:
                        typeof d?.call?.message === "string"
                            ? d.call.message
                            : typeof d?.call === "string"
                            ? d.call
                            : null,
                    email: typeof d?.email === "string" ? d.email : null,
                    sms: typeof d?.sms === "string" ? d.sms : null,
                    date:
                        typeof d?.date !== undefined
                            ? transformDate(d.date)
                            : null,
                };
            }),
        ],
    };
    if (props.campaignType === "scheduled") {
        fmtCampaign.send_at = fmtCampaign.days[0].date;
        if (fmtCampaign.days[0]?.email) {
            fmtCampaign.email_template_id = fmtCampaign.days[0].email;
        }
        if (fmtCampaign.days[0]?.sms) {
            fmtCampaign.sms_template_id = fmtCampaign.days[0].sms;
        }
        if (fmtCampaign.days[0]?.call) {
            fmtCampaign.call_template_id = fmtCampaign.days[0].call;
        }
        delete fmtCampaign.days;
    }
    console.log({ fmtCampaign, days: days.value });

    try {
        loading.value = true;
        console.log("sending campaign", fmtCampaign);

        /** route helpers */
        //debugger;
        let action = props.isNew ? "store" : "update";
        let campType = props.campaignType === "drip" ? "drip" : "scheduled";
        let endp = `mass-comms.${campType}-campaigns.${action}`;

        console.log("sending to endpoint", endp);

        const res = props.isNew
            ? await axios.post(route(endp), {
                  ...fmtCampaign,
              })
            : await axios.put(route(endp, props.existingId), {
                  ...fmtCampaign,
              });

        if (res.status === 200) {
            toastInfo("Campaign Saved!");
            loading.value = false;
            emit("done");
        }
        console.log("data received back from server:", res.data);
    } catch (err) {
        loading.value = false;
        toastError("Problem Saving Campaign");
        console.log("problem saving campaign", err);
    }
};
</script>
