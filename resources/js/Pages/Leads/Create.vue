<template>
    <ModalableWrapper>
        <LayoutHeader title="Create New Lead">
            <h2 class="font-semibold text-xl leading-tight">Create New Lead</h2>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
            <lead-form
                :user-id="user_id"
                :locations="locations"
                :lead_types="lead_types"
                :lead_statuses="lead_statuses"
                :lead_sources="lead_sources"
                :lead_owners="lead_owners"
                :available_services="available_services"
            />
        </template>
    </ModalableWrapper>
</template>

<script>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import Button from "@/Components/Button.vue";
import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";

import LeadForm from "@/Pages/Leads/Partials/LeadForm.vue";

export default defineComponent({
    components: {
        LayoutHeader,
        Button,
        JetFormSection,
        ModalableWrapper,
        ModalSlot,
        JetInputError,
        JetLabel,
        LeadForm,
    },
    props: [
        "user_id",
        "locations",
        "lead_types",
        "lead_sources",
        "available_services",
        "lead_owners",
        "lead_statuses",
    ],
    mounted() {
        console.log("lead_statuses ", this.$page.props.lead_statuses);
    },
});
</script>
