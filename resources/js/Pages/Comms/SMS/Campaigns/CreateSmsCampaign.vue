<template>
    <app-layout title="Create SMS Template">
        <template #header>
            <h2 class="font-semibold text-xl leading-tight">
                Create SMS Campaign
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <sms-campaign-form
                    :client-id="$page.props.user.current_client_id"
                    :can-activate="false"
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

import SmsCampaignForm from "./Partials/SmsCampaignForm";

export default {
    name: "CreateSmsCampaign",
    components: {
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        SmsCampaignForm,
    },
};
</script>

<style scoped></style>
