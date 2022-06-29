<template>
    <ModalableWrapper>
        <LayoutHeader title="Edit Email Campaign">
            <h2 class="font-semibold text-xl leading-tight">
                Configure & Modify Email Campaign
            </h2>
            <small>{{ campaign.name }}</small>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
            <email-template-form
                :client-id="$page.props.user.current_client_id"
                :campaign="campaign"
                :can-activate="true"
                :templates="emailTemplates"
                :audiences="audiences"
                :assigned-template="assignedTemplate"
                :assigned-audience="assignedAudience"
            />
        </template>
    </ModalableWrapper>
</template>

<script>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";

import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";

import EmailTemplateForm from "./Partials/EmailCampaignForm";

import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";

export default {
    name: "EditEmailCampaign",
    components: {
        LayoutHeader,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        EmailTemplateForm,
        ModalableWrapper,
        ModalSlot,
    },
    props: {
        campaign: {
            required: true,
            type: Object,
        },
        emailTemplates: {
            required: true,
            type: Array,
        },
        audiences: {
            required: true,
            type: Array,
        },
        assignedTemplate: {
            required: false,
            type: String,
        },
        assignedAudience: {
            required: false,
            type: String,
        },
    },
};
</script>

<style scoped></style>
