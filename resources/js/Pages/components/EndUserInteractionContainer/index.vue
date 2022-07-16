<template>
    <div class="flex flex-col items-center mb-4">
        <div class="w-full mb-8 mt-4">
            <div class="grid grid-cols-12 w-full gap-4">
                <div class="user-info-container">
                    <user-info
                        :agreementNumber="agreementNumber"
                        :editUrl="route('data.leads.edit', leadId)"
                        :fullName="fullName"
                        :opportunity="opportunity"
                        :trialDates="trialDates"
                        :trialMemberships="trialMemberships"
                    />
                    <div class="user-action-container" v-if="claimedByUser">
                        <user-actions
                            :count="interactionCount"
                            :handleClick="
                                (value) => (activeContactMethod = value)
                            "
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
                            :id="leadId"
                            @done="$refs.showViewModal.close()"
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
import UserInfo from "./UserInfo/index.vue";
import UserActions from "./UserActions/index.vue";

const props = defineProps({
    userId: {
        type: String,
    },
    leadId: {
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
    agreementNumber: {
        type: Number,
    },
    ownerUserId: {
        type: Number,
    },
});
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
            case "High":
                color = "green";
                break;

            case "Medium":
                color = "yellow";
                break;

            case "Low":
                color = "red";
                break;
        }

        return {
            "border-color": color,
            "border-width": "5px",
        };
    },
});
const claimedByUser = computed({
    get() {
        let r = false;
        if ((props.owner_user_id = props.userId)) {
            r = true;
        }
        return r;
    },
});
const modalTitle = computed({
    get() {
        switch (activeContactMethod.value) {
            case "email":
                return "Email Lead";
            case "phone":
                return "Call Lead";
            case "sms":
                return "Text Lead";
        }
    },
});
const commsHistoryRef = ref(null);
function goToLeadDetailIndex(index) {
    console.log("goToLeadDetailIndex", { index, commsHistoryRef });
    if (index !== undefined && index !== null) {
        commsHistoryRef.value.goToLeadDetailIndex(index);
    }
}
const showViewModal = ref(null);
watch([activeContactMethod], () => {
    activeContactMethod.value && showViewModal.value.open();
});
defineExpose({
    goToLeadDetailIndex,
});
</script>
