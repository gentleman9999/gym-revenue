<template>
    <div class="flex flex-col items-center mb-4">
        <div class="w-full mb-8 mt-4">
            <div class="grid grid-cols-12 w-full gap-4">
                <div class="user-info-container">
                    <end-user-info
                        :agreementId="agreementId"
                        :editUrl="route(`data.${endUserType}s.edit`, id)"
                        :fullName="fullName"
                        :opportunity="opportunity"
                        :trialDates="trialDates"
                        :trialMemberships="trialMemberships"
                    />
                    <div
                        class="user-action-container"
                        v-if="owner_user_id == userId"
                    >
                        <end-user-actions
                            :count="interactionCount"
                            :handleClick="toggleContactModal"
                            :end-user-type="endUserType"
                        />
                    </div>
                </div>
                <div class="comms-history-container">
                    <comms-history :details="details" ref="commsHistoryRef" />
                </div>
                <daisy-modal
                    id="showViewModal"
                    ref="showViewModal"
                    @close="activeContactMethod = ''"
                >
                    <div class="comms-action-container">
                        <comms-actions
                            :activeContactMethod="activeContactMethod"
                            :phone="phone"
                            :id="id"
                            @done="$refs.showViewModal.close()"
                            :end-user-type="endUserType"
                        />
                    </div>
                </daisy-modal>
            </div>
        </div>
    </div>
</template>
<style scoped>
.user-info-container {
    @apply col-span-12 lg:col-span-4 flex-shrink-0 bg-base-300 rounded-lg flex flex-col p-4;
}
.user-action-container {
    @apply flex flex-row mt-8 self-center;
}
.comms-history-container {
    @apply col-span-12 lg:col-span-8 rounded-lg bg-base-300 p-4;
}
.comms-action-container {
    @apply col-span-12 lg:col-span-8 rounded-lg bg-base-300 p-4;
}
</style>
<script setup>
import { ref, watch } from "vue";
import { computed } from "@vue/reactivity";
import CommsHistory from "./CommsHistory/index.vue";
import CommsActions from "./CommsActions/index.vue";

import DaisyModal from "@/Components/DaisyModal.vue";
import EndUserInfo from "./EndUserInfo/index.vue";
import EndUserActions from "./EndUserActions/index.vue";

const props = defineProps({
    endUserType: {
        type: String,
        default: "lead",
    },
    id: {
        type: String,
    },
    firstName: {
        type: String,
    },
    middleName: {
        type: String,
    },
    lastName: {
        type: String,
    },
    email: {
        type: String,
    },
    phone: {
        type: String,
    },
    opportunity: {
        type: String,
    },
    details: {
        type: Array,
    },
    trialDates: {
        type: String,
    },
    trialMemberships: {
        type: Array,
    },
    trialMembershipTypes: {
        type: Array,
    },
    interactionCount: {
        type: Number,
    },
    agreementId: {
        type: Number,
    },
    ownerUserId: {
        type: Number,
    },
    hasTwilioConversation: {
        type: Boolean,
        required: true,
    },
});

const showViewModal = ref(null);
const commsHistoryRef = ref(null);
const activeContactMethod = ref("");

const fullName = computed({
    get() {
        return [props.firstName, props.middleName, props.lastName].join(" ");
    },
});

const borderStyle = computed({
    get() {
        let color = "transparent";

        switch (props.opportunity) {
            case 3:
                color = "green";
                break;

            case 2:
                color = "yellow";
                break;

            case 1:
                color = "red";
                break;
        }

        return {
            "border-color": color,
            "border-width": "5px",
        };
    },
});

watch([activeContactMethod], () => {
    activeContactMethod.value && showViewModal.value.open();
});

defineExpose({
    goToEndUserDetailIndex,
});

function goToEndUserDetailIndex(index) {
    if (index !== undefined && index !== null) {
        commsHistoryRef.value.goToEndUserDetailIndex(index);
    }
}

function toggleContactModal(type) {
    // If conversation is enabled, we want to redirect the user to the chat page.
    if (type === "sms" && props.hasTwilioConversation) {
        window.location.href = route("end-user-chat", [
            props.endUserType,
            props.id,
        ]);
        return;
    }

    activeContactMethod.value = type;
}
</script>
