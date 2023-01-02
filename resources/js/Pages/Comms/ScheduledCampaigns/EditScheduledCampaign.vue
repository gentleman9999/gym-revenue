<template>
    <LayoutHeader title="dit ScheduledCampaign">
        <h2 class="font-semibold text-xl leading-tight">
            Configure & Modify Scheduled Campaign
        </h2>
        <small>{{ scheduledCampaign.name }}</small>
    </LayoutHeader>

    <scheduled-campaign-form
        :scheduled-campaign="scheduledCampaign"
        :template_types="template_types"
        :audiences="audiences"
        :email-templates="emailTemplates"
        :sms-templates="smsTemplates"
    />
</template>

<script setup>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import ScheduledCampaignForm from "./Partials/ScheduledCampaignForm.vue";

const props = defineProps({
    scheduledCampaign: {
        required: true,
        type: Object,
    },
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
