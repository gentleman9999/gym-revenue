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
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";

import EmailCampaignForm from "./Partials/EmailCampaignForm.vue";

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
