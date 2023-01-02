<template>
    <LayoutHeader title="Edit Drip Campaign">
        <h2 class="font-semibold text-xl leading-tight">
            Configure & Modify Drip Campaign
        </h2>
        <small>{{ dripCampaign.name }}</small>
    </LayoutHeader>

    <drip-campaign-form
        :can-activate="false"
        :drip-campaign="dripCampaign"
        :audiences="audiences"
    />
</template>

<script setup>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";

import DripCampaignForm from "./Partials/DripCampaignForm.vue";

const props = defineProps({
    dripCampaign: {
        required: true,
        type: Object,
    },
    audiences: {
        type: Array,
        required: true,
    },
});
</script>

<style scoped></style>
