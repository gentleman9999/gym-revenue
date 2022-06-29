<template>
    <ModalableWrapper>
        <LayoutHeader title="Create Email Campaign">
            <h2 class="font-semibold text-xl leading-tight">
                Create Email Campaign
            </h2>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
            <email-campaign-form
                :client-id="$page.props.user.current_client_id"
                :can-activate="false"
                :templates="emailTemplates"
                :audiences="audiences"
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

import EmailCampaignForm from "./Partials/EmailCampaignForm";

import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";

export default {
    name: "CreateEmailCampaign",
    props: {
        emailTemplates: {
            required: true,
            type: Array,
        },
        audiences: {
            required: true,
            type: Array,
        },
    },
    components: {
        LayoutHeader,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        EmailCampaignForm,
        ModalableWrapper,
        ModalSlot,
    },
};
</script>

<style scoped></style>
