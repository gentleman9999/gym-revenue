<template>
    <div>
        <div class="header">
            <h1>{{ heading }}</h1>
            <p>{{ date }}</p>
            <p v-if="detail?.misc?.user?.email">
                REP: {{ detail.misc.user.email }}
            </p>
            <p v-else-if="detail?.misc?.user_id">
                REP: {{ detail.misc.user_id }}
            </p>
        </div>
        <comms-email-history
            :detail="detail"
            v-if="detail.field === 'emailed_by_rep'"
        />
        <comms-sms-history
            :detail="detail"
            v-if="detail.field === 'sms_by_rep'"
        />
        <comms-phone-history
            :detail="detail"
            v-if="detail.field === 'called_by_rep'"
        />
        <comms-trial-history
            :detail="detail"
            v-if="
                detail.field === 'trial-used' ||
                detail.field === 'trial-started'
            "
        />
        <div class="form-control" v-if="detail.field === 'note_created'">
            <label class="label">
                <span class="label-text">Note</span>
            </label>
            <div class="textarea textarea-ghost h-full">
                {{ detail.value }}
            </div>
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
</style>
<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import CommsPhoneHistory from "./CommsPhoneHistory.vue";
import CommsEmailHistory from "./CommsEmailHistory.vue";
import CommsSmsHistory from "./CommsSmsHistory.vue";
import CommsTrialHistory from "./CommsTrialHistory.vue";

const props = defineProps({
    detail: {
        type: Object,
        required: true,
    },
});
const page = usePage();
const trialMembershipTypes = page.props.value.trialMembershipTypes;

const heading = computed(() => {
    switch (props.detail.field) {
        case "called_by_rep":
            return "Phone Call";
        case "emailed_by_rep":
            return "Email";
        case "sms_by_rep":
            return "Text Message";
        case "claimed":
            return "Lead Claimed";
        case "created":
            return "Lead Created";
        case "updated":
            return "Lead Updated";
        case "manual_create":
            return "Lead Manually Created in Gym Revenue";
        case "trial-started":
            return "Trial Started";
        case "trial-used":
            return "Trial Used";
        case "note_created":
            return "Note Created";
        default:
            console.error("what is this field?!?!", props.detail.field);
            break;
    }
});

const date = computed(() => {
    return new Date(props.detail.created_at).toLocaleString();
});
</script>
