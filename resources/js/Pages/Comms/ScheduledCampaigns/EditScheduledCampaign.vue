<template>
    <ModalableWrapper>
        <LayoutHeader title="dit ScheduledCampaign">
            <h2 class="font-semibold text-xl leading-tight">
                Configure & Modify Scheduled Campaign
            </h2>
            <small>{{ scheduledCampaign.name }}</small>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
            <scheduled-campaign-form
                :scheduled-campaign="scheduledCampaign"
                :template_types="template_types"
                :audiences="audiences"
                :email-templates="emailTemplates"
                :sms-templates="smsTemplates"
            />
        </template>
    </ModalableWrapper>
</template>

<script>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";

import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";
import ScheduledCampaignForm from "./Partials/ScheduledCampaignForm.vue";

export default {
    components: {
        LayoutHeader,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        ModalableWrapper,
        ModalSlot,
        ScheduledCampaignForm,
    },
    props: {
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
    },
};
</script>

<style scoped></style>
