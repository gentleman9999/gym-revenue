<template>
    <ModalableWrapper>
        <LayoutHeader title="Create Email Campaign">
            <h2 class="font-semibold text-xl leading-tight">
                Create Drip Campaign
            </h2>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
            <drip-campaign-form
                :can-activate="false"
                :template_types="template_types"
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

import DripCampaignForm from "./Partials/DripCampaignForm.vue";
import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";

export default {
    props: {
        audiences: {
            type: Array,
            required: true,
        },
    },
    components: {
        LayoutHeader,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        DripCampaignForm,
        ModalableWrapper,
        ModalSlot,
    },
};
</script>

<style scoped></style>
