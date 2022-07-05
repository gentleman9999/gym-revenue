<template>
    <ModalableWrapper>
        <LayoutHeader title="Edit Lead">
            <jet-bar-icon type="g0back" fill />
            <h2 class="font-semibold text-xl leading-tight">Edit Lead</h2>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
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
        </template>
    </ModalableWrapper>
</template>

<script>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import JetBarIcon from "@/Components/JetBarIcon.vue";

import LeadForm from "@/Pages/Leads/Partials/LeadForm.vue";
import { defineComponent } from "vue";
import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";

export default defineComponent({
    components: {
        LayoutHeader,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        JetBarIcon,
        LeadForm,
        ModalableWrapper,
        ModalSlot,
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
