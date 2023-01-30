<template>
    <LayoutHeader title="Create Email Campaign">
        <h2 class="font-semibold text-xl leading-tight">
            Create Drip Campaign
        </h2>
    </LayoutHeader>

    <drip-campaign-form
        :can-activate="false"
        :template_types="template_types"
        :audiences="audiences"
    />
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import DripCampaignForm from "./Partials/DripCampaignForm.vue";

const props = defineProps({
    audiences: {
        type: Array,
        required: true,
    },
});
</script>

<style scoped></style>
