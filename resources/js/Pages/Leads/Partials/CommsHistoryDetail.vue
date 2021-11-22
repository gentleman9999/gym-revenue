<template>
    <div>
        <div class="header">
            <h1>{{ heading }}</h1>
            <p>{{ date }}</p>
            <p v-if="detail?.misc?.user?.email">REP: {{ detail.misc.user.email }}</p>
            <p v-else-if="detail?.misc?.user_id">REP: {{ detail.misc.user_id }}</p>
        </div>

        <div class="form-control" v-if="detail.field === 'emailed_by_rep'">
            <label class="label">
                <span class="label-text">Subject</span>
            </label>
            <div type="text" readonly class="input input-ghost h-full" style="height:100%;">
                {{ detail.misc.subject }}
            </div>
        </div>

        <div class="form-control" v-if="detail.field === 'emailed_by_rep'">
            <label class="label">
                <span class="label-text">Message</span>
            </label>
            <div type="text" readonly class="textarea textarea-ghost h-full" style="height:100%;">
                {{ detail.misc.message }}
            </div>
        </div>


        <div class="form-control" v-if="detail.field === 'called_by_rep'">
            <label class="label">
                <span class="label-text">Outcome</span>
            </label>
            <div type="text" readonly class="input input-ghost h-full" style="height:100%;">
                {{ outcome }}
            </div>
        </div>

        <div class="form-control" v-if="detail.field === 'called_by_rep'">
            <label class="label">
                <span class="label-text">Notes</span>
            </label>
            <div type="text" readonly class="textarea textarea-ghost h-full" style="height:100%;">
                {{ detail.misc.notes }}
            </div>
        </div>

        <div class="form-control" v-if="detail.field === 'sms_by_rep'">
            <label class="label">
                <span class="label-text">Message</span>
            </label>
            <div type="text" readonly class="textarea textarea-ghost h-full" style="height:100%;">
                {{ detail.misc.message }}
            </div>
        </div>
        <div class="response"  v-if="detail.field === 'emailed_by_rep'">
            <h1 class="">Reply</h1>
            <email-comms-action :lead-id="detail.lead_id" hide-help-text hide-subject :subject="detail.misc.subject" @done="$emit('done')"/>
        </div>
        <div class="response"  v-if="detail.field === 'called_by_rep'">
            <h1 class="">Follow Up</h1>
            <phone-comms-action :lead-id="detail.lead_id" :phone="detail.phone" hide-help-text @done="$emit('done')"/>
        </div>
        <div class="response"  v-if="detail.field === 'sms_by_rep'">
            <h1 class="">Reply</h1>
            <sms-comms-action :lead-id="detail.lead_id" hide-help-text @done="$emit('done')"/>
        </div>
    </div>
</template>
<style scoped>
.header {
    @apply bg-primary px-4 py-4 rounded-lg;

    h1 {
        @apply font-bold text-2xl;
    }

    p {
        @apply opacity-50;
    }
}
.response{
    @apply rounded-lg bg-primary m-4 p-4;
    h1{
        @apply text-center text-xl font-bold;
    }
}
</style>
<script>
import {computed} from 'vue';
import EmailCommsAction from "./EmailCommsAction";
import PhoneCommsAction from "./PhoneCommsAction";
import SmsCommsAction from "./SmsCommsAction";

export default {
    components: {SmsCommsAction, PhoneCommsAction, EmailCommsAction},
    props: {
        detail: {
            type: Object,
            required: true
        }
    },
    emits: ['done'],
    setup(props) {
        const heading = computed(() => {
            switch (props.detail.field) {
                case "called_by_rep":
                    return 'Phone Call';
                case "emailed_by_rep":
                    return 'Email';
                case "sms_by_rep":
                    return 'Text Message';
                case "claimed":
                    return 'Lead Claimed';
                case "created":
                    return 'Lead Created';
                case "updated":
                    return 'Lead Updated';
                case "manual_create":
                    return "Lead Manually Created in Gym Revenue";
                default:
                    console.error('what is this field?!?!', props.detail.field);
                    break;
            }
        });

        const outcome = computed(() => {
            switch (props.detail.misc?.outcome) {
                case 'contacted':
                    return 'Spoke with Lead.';
                case 'voicemail':
                    return 'Left a Voicemail';
                case 'hung-up':
                    return 'Lead Hung Up';
                case 'wrong-number':
                    return 'Wrong Number';
                case 'appointment':
                    return 'An Appointment Was Scheduled';
                case 'sale':
                    return 'Made the Sale over the Phone!';
            }
        });

        const date = computed(() => {
            return new Date(props.detail.created_at).toLocaleString();
        })

        return {heading, date, outcome}
    }
}
</script>
