<template>
    <div class="form-control">
        <label class="label">
            <span class="label-text">Trial Membership</span>
        </label>
        <div class="textarea textarea-ghost h-full">
            {{ membership.type_name }}
        </div>
        <label class="label">
            <span class="label-text">Duration</span>
        </label>
        <div class="textarea textarea-ghost h-full">
            {{ membership.trial_length }}
        </div>
    </div>
</template>
<style scoped>
.response {
    @apply rounded-lg bg-primary md:m-4 p-4;
    h1 {
        @apply text-center text-xl font-bold;
    }
}
</style>
<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
const page = usePage();
const trialMembershipTypes = page.props.value.trialMembershipTypes;
const props = defineProps({
    detail: {
        type: Object,
        required: true,
    },
});

const membership = computed({
    get() {
        return trialMembershipTypes.find(
            (trial) => trial.id === props.detail.misc.trial_id
        );
    },
});
</script>
