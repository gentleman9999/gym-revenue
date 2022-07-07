<template>
    <ModalableWrapper>
        <LayoutHeader title="Create SMS Campaign">
            <h2 class="font-semibold text-xl leading-tight">
                Create Scheduled Campaign
            </h2>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
            <scheduled-campaign-form
                :can-activate="false"
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

import ScheduledCampaignForm from "./Partials/ScheduledCampaignForm.vue";

import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";

export default {
    props: {
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
};
</script>

<style scoped></style>
