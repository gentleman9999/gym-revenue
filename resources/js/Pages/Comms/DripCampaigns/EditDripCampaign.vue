<template>
    <ModalableWrapper>
        <LayoutHeader title="Edit Drip Campaign">
            <h2 class="font-semibold text-xl leading-tight">
                Configure & Modify Drip Campaign
            </h2>
            <small>{{ dripCampaign.name }}</small>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
            <drip-campaign-form
                :can-activate="false"
                :drip-campaign="dripCampaign"
                :audiences="audiences"
            />
        </template>
    </ModalableWrapper>
</template>

<script>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";

import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";
import DripCampaignForm from "./Partials/DripCampaignForm.vue";

export default {
    components: {
        LayoutHeader,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        ModalableWrapper,
        ModalSlot,
        DripCampaignForm,
    },
    props: {
        dripCampaign: {
            required: true,
            type: Object,
        },
        audiences: {
            type: Array,
            required: true,
        },
    },
};
</script>

<style scoped></style>
