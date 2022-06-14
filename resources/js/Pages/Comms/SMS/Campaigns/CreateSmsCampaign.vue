<template>
    <ModalableWrapper>
        <app-layout title="Create SMS Template">
            <template #header>
                <h2 class="font-semibold text-xl leading-tight">
                    Create SMS Campaign
                </h2>
            </template>

            <div>
                <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                    <ModalSlot />
                </div>
            </div>
        </app-layout>
        <template #modal>
            <sms-campaign-form
                :client-id="$page.props.user.current_client_id"
                :can-activate="false"
            />
        </template>
    </ModalableWrapper>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";

import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";

import SmsCampaignForm from "./Partials/SmsCampaignForm";

import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";

export default {
    name: "CreateSmsCampaign",
    components: {
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        SmsCampaignForm,
        ModalableWrapper,
        ModalSlot,
    },
};
</script>

<style scoped></style>
