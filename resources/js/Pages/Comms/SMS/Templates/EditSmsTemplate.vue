<template>
    <ModalableWrapper>
        <app-layout title="Edit SMS Template">
            <template #header>
                <h2 class="font-semibold text-xl leading-tight">
                    Edit SMS Template
                </h2>
            </template>

            <div>
                <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                    <ModalSlot />
                </div>
            </div>
        </app-layout>
        <template #modal>
            <sms-template-form
                :client-id="$page.props.user.current_client_id"
                :template="template"
                :can-activate="true"
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

import SmsTemplateForm from "./Partials/SmsTemplateForm";

import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";

export default defineComponent({
    components: {
        AppLayout,
        Button,
        JetFormSection,

        JetInputError,
        JetLabel,
        SmsTemplateForm,
        ModalableWrapper,
        ModalSlot,
    },
    props: {
        template: {
            required: true,
            type: Object,
        },
    },
});
</script>
