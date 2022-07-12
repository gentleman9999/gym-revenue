<template>
    <LayoutHeader title="Members" />
    <jet-bar-container>
        <lead-interaction
            :lead-id="member.id"
            :user-id="$page.props.user.id"
            :first-name="member.first_name"
            :middle-name="$page.props.middle_name.value"
            :last-name="member.last_name"
            :email="member.email"
            :phone="member.primary_phone"
            :details="member['details_desc']"
            ref="leadInteractionRef"
            :selectedLeadDetailIndex="selectedLeadDetailIndex"
            :trial-dates="trialDates"
            :trial-memberships="member.trial_memberships"
            :interaction-count="interactionCount"
        />
    </jet-bar-container>
</template>

<script>
import { defineComponent, ref, onMounted, watch, watchEffect } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import JetBarContainer from "@/Components/JetBarContainer.vue";
// import LeadInteraction from "./Partials/LeadInteractionContainer.vue";

export default defineComponent({
    components: {
        LayoutHeader,
        JetBarContainer,
        // LeadInteraction,
    },
    props: {
        member: {
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
    },
    setup(props) {
        const leadInteractionRef = ref();
        const selectedLeadDetailIndex = props.flash?.selectedLeadDetailIndex;

        watchEffect(() => {
            leadInteractionRef.value?.goToLeadDetailIndex(
                props.flash?.selectedLeadDetailIndex
            );
        });

        return { leadInteractionRef };
    },
});
</script>

<style scoped></style>
