<template>
    <div class="flex flex-col items-center mx-4 min-w-[10rem]">
        <p
            v-if="multiday"
            class="flex items-center px-2 py-1 border border-secondary font-bold rounded-md"
        >
            <button
                :disabled="disableSubtractDay"
                @click="subtractFromDay"
                class="bg-secondary rounded-full flex justify-center items-center h-4 w-4 mr-2 disabled:bg-red-600 disabled:opacity-75"
            >
                -
            </button>
            Day {{ campaignDayIx }}
            <button
                :disabled="disableAddDay"
                @click="addToDay"
                class="bg-secondary rounded-full flex justify-center items-center h-4 w-4 ml-2 disabled:bg-red-600 disabled:opacity-75"
            >
                +
            </button>
        </p>

        <div v-if="!multiday">
            <date-picker
                v-model="chosenDate"
                dark
                :min-date="new Date()"
                :flow="['month', 'date', 'time']"
            />
        </div>

        <div class="h-8 w-[0.125rem] bg-base-content my-2"></div>

        <div
            class="bg-lime-800 bg-opacity-50 pt-2 px-2 rounded-md border border-lime-600 flex"
        >
            <ContactButton
                @reset="$emit('reset-email', index)"
                :handler="() => $emit('email', index)"
                :active="mailActive"
            >
                <Mail :active="mailActive" />
            </ContactButton>

            <ContactButton
                @reset="$emit('reset-sms', index)"
                :handler="() => $emit('sms', index)"
                class="mx-4"
                :active="smsActive"
            >
                <Message :active="smsActive" />
            </ContactButton>

            <ContactButton
                @reset="$emit('reset-call', index)"
                :handler="() => $emit('phone', index)"
                :active="phoneActive"
            >
                <Phone :active="phoneActive" />
            </ContactButton>
        </div>
        <div class="h-8 w-[0.125rem] bg-base-content my-2"></div>

        <button
            class="px-2 py-1 border-secondary border rounded-md hover:bg-secondary transition-all"
        >
            Performance
        </button>
        <button
            class="px-2 py-1 border-secondary border rounded-md mt-4 hover:bg-secondary transition-all"
        >
            Conversions
        </button>
    </div>
</template>

<script setup>
import { ref, watch, computed } from "vue";

import Mail from "./icons/Mail.vue";
import Message from "./icons/Message.vue";
import Phone from "./icons/Phone.vue";
import ContactButton from "./ContactButton.vue";
import DatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";

import { faPlus } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";

library.add(faPlus);

const emit = defineEmits(["email", "sms", "phone", "date"]);

const props = defineProps({
    index: {
        type: Number,
        default: 0,
    },
    day_ix: {
        type: Number,
        default: 0,
    },
    mailActive: {
        type: Boolean,
        default: false,
    },
    smsActive: {
        type: Boolean,
        default: false,
    },
    phoneActive: {
        type: Boolean,
        default: false,
    },
    multiday: {
        type: Boolean,
        default: true,
    },
    date: {
        type: [Date, String, Number],
        default: new Date(),
    },
    all_days: {
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
});

const chosenDate = ref(props.date);
const campaignDayIx = ref(props.day_ix);
const days = ref(props.all_days);

const lowerLimit = computed(() => {
    if (props.index === 0) return 0;

    let previousDay = days.value[props.index - 1];

    if (typeof previousDay?.day_in_campaign === "number") {
        return previousDay.day_in_campaign;
    } else return 0;
});

const upperLimit = computed(() => {
    let nextDay = props.all_days[props.index + 1];
    if (typeof nextDay === "undefined") return 8888;

    if (typeof nextDay?.day_in_campaign === "number") {
        return nextDay.day_in_campaign;
    } else return 999999;
});

const disableSubtractDay = computed(() => {
    if (props.day_ix <= 1) return true;
    if (props.day_ix - 1 <= lowerLimit.value) return true;
    return false;
});

const disableAddDay = computed(() => {
    if (props.day_ix + 1 === upperLimit.value) return true;
    return false;
});

const subtractFromDay = () => {
    campaignDayIx.value = campaignDayIx.value - 1;
    emit("day_ix", campaignDayIx.value);
};

const addToDay = () => {
    campaignDayIx.value = campaignDayIx.value + 1;
    emit("day_ix", campaignDayIx.value);
};

watch([chosenDate, campaignDayIx], () => {
    emit("date", chosenDate.value);
});
</script>
