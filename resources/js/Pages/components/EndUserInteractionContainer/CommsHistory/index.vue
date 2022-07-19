<template>
    <div class="space-y-2">
        <ul class="w-full steps">
            <comms-history-step
                v-for="detail in details"
                :key="detail.lead_id"
                :detail="detail"
                @click="selectedDetail = detail"
                :active="selectedDetail?.id === detail.lead_id"
            />
        </ul>
        <comms-history-detail :detail="selectedDetail" v-if="selectedDetail" />
        <span v-else class="comms-hint">
            Click on an item above to learn more
        </span>
    </div>
</template>
<style scoped>
.comms-hint {
    @apply block py-8 w-full text-center self-center m-auto opacity-50 text-xl;
}
</style>
<script setup>
import { ref } from "vue";
import CommsHistoryStep from "./CommsHistoryStep.vue";
import CommsHistoryDetail from "./CommsHistoryDetail.vue";

const props = defineProps({
    details: {
        type: Array,
        required: true,
    },
    trialMembershipTypes: {
        type: Array,
        default: [],
    },
});

const selectedDetail = ref(null);
const goToEndUserDetailIndex = (index) => {
    if (index in props.details) {
        selectedDetail.value = props.details[index];
    }
};
defineExpose({
    goToEndUserDetailIndex,
});
</script>
