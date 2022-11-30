<template>
    <base-comms-action
        :id="id"
        submit-text="Send"
        :form="form"
        :allow-submit="isFormValid"
        :end-user-type="endUserType"
        @done="$emit('done')"
    >
        <div v-if="!hideHelpText">
            <p>Contact Via Email</p>
            <p>
                <i>{{ help }}</i>
            </p>
        </div>
        <div class="flex flex-col mb-4" v-if="!hideSubject">
            <label>Email Subject</label>
            <input class="form-control" v-model="form.subject" type="text" />
        </div>
        <div class="flex flex-col mb-4">
            <label>Email Message</label>
            <textarea
                class="form-control"
                v-model="form.message"
                rows="4"
                cols="38"
            ></textarea>
        </div>
    </base-comms-action>
</template>

<script setup>
import { computed } from "vue";
import { useGymRevForm } from "@/utils";
import BaseCommsAction from "./BaseCommsAction.vue";
const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    hideHelpText: {
        type: Boolean,
        default: false,
    },
    hideSubject: {
        type: Boolean,
        default: false,
    },
    subject: {
        type: String,
    },
    endUserType: {
        type: String,
        required: true,
    },
});
const form = useGymRevForm({
    method: "email",
    subject: props.subject,
    message: null,
});

const isFormValid = computed(
    () => form.subject?.length && form.message?.length
);

const help =
    "Enter a subject and a message in the body and click Create Message to send to your customer.";
const emit = defineEmits(["done"]);
</script>
