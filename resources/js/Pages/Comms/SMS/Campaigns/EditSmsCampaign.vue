<template>
    <app-layout title="Edit SMS Template">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">
                Configure & Modify SMS Campaign
            </h2>
            <small>{{ campaign.name }}</small>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <sms-template-form
                    :client-id="$page.props.user.current_client_id"
                    :campaign="campaign"
                    :can-activate="true"
                    :templates="templates"
                    :audiences="audiences"
                    :assigned-template="assignedTemplate"
                    :assigned-audience="assignedAudience"
                />
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";

import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";

import SmsTemplateForm from "./Partials/SmsCampaignForm";

export default {
    name: "EditSmsCampaign",
    components: {
        AppLayout,
        Button,
        JetFormSection,

        JetInputError,
        JetLabel,
        SmsTemplateForm,
    },
    props: {
        campaign: {
            required: true,
            type: Object,
        },
        templates: {
            required: true,
            type: Array,
        },
        audiences: {
            required: true,
            type: Array,
        },
        assignedTemplate: {
            required: false,
            type: String
        },
        assignedAudience: {
            required: false,
            type: String
        }

    },
}
</script>

<style scoped>

</style>
