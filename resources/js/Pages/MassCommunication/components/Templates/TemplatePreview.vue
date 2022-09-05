<template>
    <div class="flex flex-col items-center min-w-[12rem] mx-1" :id="temp_id">
        <div
            v-if="template_type !== 'sms'"
            :style="`background-image: url(${thumbsrc})`"
            class="h-48 w-40 bg-white border-secondary border relative bg-no-repeat bg-contain bg-center"
        >
            <button
                @click="$emit('submit', temp_id)"
                class="bg-secondary bg-opacity-50 absolute top-0 left-0 h-full w-full opacity-0 hover:opacity-100 transition-all duration-300"
                :class="{ 'opacity-70': selected }"
            >
                <div
                    :id="thumbId"
                    class="text-base-content h-14 w-14 flex justify-center items-center mx-auto border-2 border-base-content rounded-full"
                >
                    <font-awesome-icon icon="check" />
                </div>
            </button>
        </div>

        <button
            class="bg-base-content bg-opacity-0 hover:bg-opacity-20 relative rounded-md py-12 px-4 transition-all duration-300"
            :class="{ '!bg-secondary !bg-opacity-50': selected }"
            @click="$emit('submit', temp_id)"
            v-if="template_type === 'sms'"
        >
            <div
                v-if="selected"
                :id="thumbId"
                class="text-base-content h-14 w-14 flex justify-center items-center mx-auto border-2 border-base-content rounded-full absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"
            >
                <font-awesome-icon icon="check" />
            </div>
            <div
                class="max-w-[10rem] max-h-20 p-4 mx-auto rounded-lg bg-white bg-opacity-30 relative whitespace-pre"
            >
                <p class="text-xs text-ellipsis overflow-hidden">
                    {{ template_item?.markup }}
                </p>
            </div>

            <p class="text-secondary py-1 mt-1">{{ title }}</p>
            <p>{{ date }}</p>
        </button>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { faCheck } from "@fortawesome/pro-solid-svg-icons";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";

library.add(faCheck);

const props = defineProps({
    title: {
        type: String,
        defaut: "Default Title",
    },
    date: {
        type: [Date, String],
        default: "Feb. 06, 2021",
    },
    temp_id: {
        type: String,
        default: "0",
    },
    selected: {
        type: Boolean,
        default: false,
    },
    thumbsrc: {
        type: String,
        default: "",
    },
    template_type: {
        type: [String, undefined],
        default: undefined,
    },
    template_item: {
        type: Object,
        default: {},
    },
});

const thumbId = ref(`thumb-${props.temp_id}`);
</script>
