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

<script setup>
import { onMounted } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import Button from "@/Components/Button.vue";
import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";
import JetFormSection from "@/Jetstream/FormSection.vue";

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";

import LeadForm from "@/Pages/Leads/Partials/LeadForm.vue";
import { usePage } from "@inertiajs/inertia-vue3";

const props = defineProps({
    user_id: {
        type: String,
    },
    locations: {
        type: Array,
    },
    lead_types: {
        type: Array,
    },
    lead_sources: {
        type: Array,
    },
    available_services: {
        type: Array,
    },
    lead_owners: {
        type: Array,
    },
    lead_statuses: {
        type: Array,
    },
});
const page = usePage();
onMounted(() => {
    console.log("lead_statuses ", page.props.lead_statuses);
});
</script>
