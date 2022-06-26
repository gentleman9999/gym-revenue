<template>
    <base-comms-action
        :lead-id="leadId"
        submit-text="Send Message"
        :form="form"
        @done="$emit('done')"
    >
        <div v-if="!hideHelpText">
            <p>Contact Via SMS</p>
            <p>
                <i
                    >This feature is best utilized to remind them of their
                    upcoming appointment or to send them their enrollment
                    URL.</i
                >
            </p>
        </div>
        <div>
            <sms-form-control name="message" v-model="form.message" />
        </div>
    </base-comms-action>
</template>

<script>
import { computed, defineComponent } from "vue";
import { useGymRevForm } from "@/utils";
import BaseCommsAction from "./BaseCommsAction";
import SmsFormControl from "@/Components/SmsFormControl";

export default defineComponent({
    components: {
        BaseCommsAction,
        SmsFormControl,
    },
    props: {
        leadId: {
            type: String,
            required: true,
        },
        charLimit: {
            type: Number,
            default: 130,
        },
        hideHelpText: {
            type: Boolean,
            default: false,
        },
    },
    emits: ["done"],
    setup(props, { emit }) {
        const form = useGymRevForm({
            method: "sms",
            message: null,
        });

        const charsUsed = computed(() => form.message?.length || 0);
        const charsLeft = computed(
            () => props.charLimit - (form.message?.length || 0)
        );

        return { charsUsed, charsLeft, form };
    },
});
</script>
