<template>
    <base-comms-action
        :lead-id="leadId"
        submit-text="Send"
        :form="form"
        :disabled="form.processing || !form.isDirty"
        :allow-submit="true"
        @done="$emit('done')"
    >
        <div v-if="!hideHelpText">
            <p>Contact Via Email</p>
            <p>
                <i
                    >Enter a subject and a message in the body and click Create
                    Message to send to your customer.</i
                >
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

<script>
import { defineComponent } from "vue";
import { useGymRevForm } from "@/utils";
import BaseCommsAction from "./BaseCommsAction.vue";

export default defineComponent({
    components: {
        BaseCommsAction,
    },
    props: {
        leadId: {
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
    },
    emits: ["done"],
    setup(props, { emit }) {
        const form = useGymRevForm({
            method: "email",
            subject: props.subject,
            message: null,
        });

        return { form };
    },
});
</script>
