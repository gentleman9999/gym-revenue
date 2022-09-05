<template>
    <div class="flex flex-row">
        <campaign-action
            v-for="(action, idx) in actions"
            :action="action"
            :style="{ 'margin-left': `-${40 * idx}%` }"
            class="transform transition-all hover:translate-y-[-10%] hover:z-[1]"
        />
    </div>
</template>
<style scoped>
.campaign-action {
    @apply flex items-center justify-center border rounded bg-neutral-900 text-base-content w-12 h-11 m-auto;
}
</style>
<script setup>
import CampaignAction from "@/Pages/MassCommunication/components/CampaignList/CampaignAction.vue";
const props = defineProps({
    actions: {
        type: Array,
        default: [],
    },
});
</script>
