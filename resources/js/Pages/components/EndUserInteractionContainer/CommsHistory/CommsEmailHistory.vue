<template>
    <div class="form-control">
        <label class="label">
            <span class="label-text">Subject</span>
        </label>
        <div class="input input-ghost h-full">
            {{ detail.misc.subject }}
        </div>
    </div>

    <div class="form-control">
        <label class="label">
            <span class="label-text">Message</span>
        </label>
        <div class="textarea textarea-ghost h-full">
            {{ detail.misc.message }}
        </div>
    </div>
    <div class="response">
        <h1>Reply</h1>
        <email-comms-action
            :id="detail.lead_id"
            hide-help-text
            hide-subject
            :subject="detail.misc.subject"
            @done="$emit('done')"
            :end-user-type="EndUserTypes"
        />
    </div>
</template>
<style scoped>
.response {
    @apply rounded-lg bg-primary m-4 p-4;
    h1 {
        @apply text-center text-xl font-bold;
    }
}
</style>
<script setup>
import EmailCommsAction from "../CommsActions/EmailCommsAction.vue";

const props = defineProps({
    detail: {
        type: Object,
        required: true,
    },
    endUserType: {
        type: String,
        default: true,
    },
});
</script>
