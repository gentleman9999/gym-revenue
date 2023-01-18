<template>
    <LayoutHeader title="Leads" />
    <jet-bar-container>
        <end-user-interaction-container
            end-user-type="lead"
            :id="lead.id"
            :user-id="$page.props.user.id"
            :first-name="lead.first_name"
            :middle-name="lead.middle_name"
            :last-name="lead.last_name"
            :opportunity="lead.opportunity"
            :email="lead.email"
            :phone="lead.phone"
            :details="lead.details"
            ref="leadInteractionRef"
            :selectedLeadDetailIndex="selectedLeadDetailIndex"
            :trial-dates="trialDates"
            :trial-memberships="lead.trial_memberships"
            :interaction-count="interactionCount"
            :agreement-number="lead.agreement_number"
            :owner-user-id="lead.owner_user_id"
            :has-twilio-conversation="hasTwilioConversation"
        />
    </jet-bar-container>
</template>

<script>
import { defineComponent, ref, onMounted, watch, watchEffect } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
import EndUserInteractionContainer from "@/Pages/components/EndUserInteractionContainer/index.vue";

export default defineComponent({
    components: {
        LayoutHeader,
        JetBarContainer,
        EndUserInteractionContainer,
    },
    props: {
        lead: {
            type: Object,
            required: true,
        },
        flash: {
            type: Object,
        },
        trialDates: {
            type: Array,
            default: [],
        },
        interactionCount: {
            type: Number,
            default: 0,
        },
        hasTwilioConversation: {
            type: Boolean,
            required: true,
        },
    },
    setup(props) {
        const leadInteractionRef = ref();
        const selectedLeadDetailIndex = props.flash?.selectedLeadDetailIndex;

        watchEffect(() => {
            leadInteractionRef.value?.goToEndUserDetailIndex(
                props.flash?.selectedLeadDetailIndex
            );
        });

        return { leadInteractionRef };
    },
});
</script>

<style scoped></style>
