<template>
    <LayoutHeader title="Create SMS Campaign">
        <h2 class="font-semibold text-xl leading-tight">
            Create Scheduled Campaign
        </h2>
    </LayoutHeader>

    <scheduled-campaign-form
        :can-activate="false"
        :template_types="template_types"
        :audiences="audiences"
        :email-templates="emailTemplates"
        :sms-templates="smsTemplates"
    />
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import ScheduledCampaignForm from "./Partials/ScheduledCampaignForm.vue";

const props = defineProps({
    template_types: {
        type: Array,
        required: true,
    },
    emailTemplates: {
        type: Array,
        required: true,
    },
    smsTemplates: {
        type: Array,
        required: true,
    },
    audiences: {
        type: Array,
        required: true,
    },
});
</script>

<style scoped></style>
