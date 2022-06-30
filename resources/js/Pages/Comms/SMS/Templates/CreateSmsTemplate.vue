<template>
    <ModalableWrapper>
        <LayoutHeader title="Create SMS Template">
            <h2 class="font-semibold text-xl leading-tight">
                Create SMS Template
            </h2>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
            <sms-template-form
                :client-id="$page.props.user.current_client_id"
                :can-activate="false"
            />
        </template>
    </ModalableWrapper>
</template>

<script>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader";
import Button from "@/Components/Button";
import JetFormSection from "@/Jetstream/FormSection";

import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";

import SmsTemplateForm from "./Partials/SmsTemplateForm";

import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";

export default defineComponent({
    components: {
        LayoutHeader,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        SmsTemplateForm,
        ModalableWrapper,
        ModalSlot,
    },
});
</script>
