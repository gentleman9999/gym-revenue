<template>
    <base-comms-action :lead-id="leadId" submit-text="Send Message" :form="form" @done="$emit('done')">
        <div>
            <div v-if="!hideHelpText">
                <p> Contact Via SMS</p>
                <p><i>This feature is best utilized to remind them of their upcoming appointment or to send them their
                    enrollment URL.</i></p>
            </div>
            <div>
                <label>Message</label>
                <textarea class="form-control" v-model="form.message" rows="4" cols="40"
                          :maxlength="charLimit"></textarea>
                <div class="col-md-12" style="text-align: right">
                    <small>Character Count - {{ charsUsed }}/{{ charLimit }}</small>
                </div>
            </div>
        </div>
    </base-comms-action>
</template>

<script>
import {computed, defineComponent} from 'vue'
import {useForm} from '@inertiajs/inertia-vue3'
import BaseCommsAction from "./BaseCommsAction";


export default defineComponent({
    components: {
        BaseCommsAction,
    },
    props: {
        leadId: {
            type: String,
            required: true
        },
        charLimit: {
            type: Number,
            default: 130
        },
        hideHelpText: {
            type: Boolean,
            default: false
        },
    },
    emits: ["done"],
    setup(props, {emit}) {
        const form = useForm({
            method: 'sms',
            message: null
        })

        const charsUsed = computed(() => form.message?.length || 0);
        const charsLeft = computed(() => props.charLimit - (form.message?.length || 0));

        return {charsUsed, charsLeft, form};
    },
});
</script>

