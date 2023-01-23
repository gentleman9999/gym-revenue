<template>
    <LayoutHeader title="Customers" />
    <jet-bar-container>
        <end-user-interaction-container
            end-user-type="customer"
            :id="customer.id"
            :user-id="$page.props.user.id"
            :first-name="customer.first_name"
            :middle-name="customer.middle_name"
            :last-name="customer.last_name"
            :opportunity="customer.opportunity"
            :email="customer.email"
            :phone="customer.primary_phone"
            :details="customer.details_desc"
            ref="customerInteractionRef"
            :selectedLeadDetailIndex="selectedLeadDetailIndex"
            :trial-dates="trialDates"
            :interaction-count="interactionCount"
            :agreement-id="customer.agreement_id"
            :owner-user-id="customer.owner_user_id"
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
        customer: {
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
        const customerInteractionRef = ref();

        watchEffect(() => {
            customerInteractionRef.value?.goToEndUserDetailIndex(
                props.flash?.selectedCustomerDetailIndex
            );
        });

        return { customerInteractionRef };
    },
});
</script>

<style scoped></style>
