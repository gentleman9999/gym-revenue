<template>
    <app-layout title="Edit Lead">
        <template #header>
            <jet-bar-icon type="g0back" fill />
            <h2 class="font-semibold text-xl leading-tight">Edit Lead</h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div class="badge badge-info" v-if="trialDates?.length">
                    Trial Uses: {{ trialDates?.length || 0 }}
                </div>
                <div
                    class="badge badge-error mt-4"
                    v-if="lead.trial_memberships?.length"
                >
                    Trial Expires:
                    {{
                        new Date(
                            lead.trial_memberships[0].expiry_date
                        ).toLocaleString()
                    }}
                </div>
                <lead-form
                    :user-id="user_id"
                    :client-id="this.$page.props.user.current_client_id"
                    :lead="lead"
                    :locations="locations"
                    :lead_types="lead_types"
                    :lead_sources="lead_sources"
                    :lead_statuses="lead_statuses"
                    :lead_owners="lead_owners"
                    :available_services="available_services"
                    :interaction-count="interactionCount"
                />
            </div>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";

import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import JetBarIcon from "@/Components/JetBarIcon";

import LeadForm from "@/Pages/Leads/Partials/LeadForm";
import { defineComponent } from "vue";

export default defineComponent({
    components: {
        AppLayout,
        Button,
        JetFormSection,

        JetInputError,
        JetLabel,
        JetBarIcon,
        LeadForm,
    },
    props: [
        "user_id",
        "lead",
        "locations",
        "lead_types",
        "lead_sources",
        "available_services",
        "trialDates",
        "lead_owners",
        "lead_statuses",
        "interactionCount",
    ],
});
</script>
