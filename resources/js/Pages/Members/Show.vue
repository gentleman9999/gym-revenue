<template>
    <LayoutHeader title="Members" />
    <jet-bar-container>
        <end-user-interaction-container
            end-user-type="member"
            :id="member.id"
            :user-id="$page.props.user.id"
            :first-name="member.first_name"
            :middle-name="member.middle_name"
            :last-name="member.last_name"
            :opportunity="member.opportunity"
            :email="member.email"
            :phone="member.primary_phone"
            :details="member.details_desc"
            ref="memberInteractionRef"
            :selectedLeadDetailIndex="selectedLeadDetailIndex"
            :trial-dates="trialDates"
            :interaction-count="interactionCount"
            :agreement-number="member.agreement_number"
            :owner-user-id="member.owner_user_id"
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
        const memberInteractionRef = ref();

        watchEffect(() => {
            memberInteractionRef.value?.goToEndUserDetailIndex(
                props.flash?.selectedMemberDetailIndex
            );
        });

        return { memberInteractionRef };
    },
});
</script>

<style scoped></style>
