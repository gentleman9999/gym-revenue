<template>
    <ModalableWrapper>
        <LayoutHeader title="Edit Email Template">
            <h2 class="font-semibold text-xl leading-tight">
                Edit Email Template
            </h2>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
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
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";

import EmailTemplateForm from "./Partials/EmailTemplateForm.vue";

export default defineComponent({
    components: {
        LayoutHeader,
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
