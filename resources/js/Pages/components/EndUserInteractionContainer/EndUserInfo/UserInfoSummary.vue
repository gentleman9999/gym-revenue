<template>
    <h1 class="text-center text-2xl">
        {{ fullName }}
    </h1>
    <div class="badge badge-success mt-4">
        Agreement #: {{ agreementNumber }}
    </div>
    <div class="badge badge-info mt-4" v-if="trialDates?.length">
        Trial Uses: {{ trialDates?.length || 0 }}
    </div>
    <div class="badge badge-error mt-4" v-if="trialMemberships?.length">
        Trial Expires:
        {{ new Date(trialMemberships[0].expiry_date).toLocaleString() }}
    </div>
</template>
<script setup>
const props = defineProps({
    fullName: {
        type: String,
    },
    agreementNumber: {
        type: Number,
    },
    trialDates: {
        type: String,
    },
    trialMemberships: {
        type: Array,
    },
});
</script>
