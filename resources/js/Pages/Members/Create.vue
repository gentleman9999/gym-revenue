<template>
    <ModalableWrapper>
        <app-layout title="Create New Member">
            <template #header>
                <h2 class="font-semibold text-xl leading-tight">
                    Create New Member
                </h2>
            </template>

            <div>
                <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                    <ModalSlot />
                </div>
            </div>
        </app-layout>
        <template #modal>
            <member-form
                :user-id="user_id"
                :client-id="$page.props.user.current_client_id"
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
import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";

import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";

import MemberForm from "@/Pages/Members/Partials/MemberForm";

import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";

export default defineComponent({
    components: {
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        MemberForm,
        ModalableWrapper,
        ModalSlot,
    },
    props: ["user_id", "locations", "available_services"],
});
</script>
