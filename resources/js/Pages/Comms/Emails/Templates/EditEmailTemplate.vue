<template>
    <ModalableWrapper>
        <app-layout title="Edit Email Template">
            <template #header>
                <h2 class="font-semibold text-xl leading-tight">
                    Edit Email Template
                </h2>
            </template>

            <div>
                <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                    <ModalSlot />
                </div>
            </div>
        </app-layout>
        <template #modal>
            <email-template-form
                :client-id="$page.props.user.current_client_id"
                :template="template"
                :can-activate="true"
                :topol-api-key="topolApiKey"
            />
        </template>
    </ModalableWrapper>
</template>

<script>
import { defineComponent } from "vue";
import AppLayout from "@/Layouts/AppLayout";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";
import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";

import EmailTemplateForm from "./Partials/EmailTemplateForm";

export default defineComponent({
    components: {
        AppLayout,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        EmailTemplateForm,
        ModalSlot,
        ModalableWrapper,
    },
    props: {
        template: {
            required: true,
            type: Object,
        },
        topolApiKey: {
            type: String,
            required: true,
        },
        plansUrl: {
            type: String,
            required: true,
        },
    },
});
</script>
