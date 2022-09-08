<template>
    <ModalableWrapper>
        <LayoutHeader title="Create Email Template">
            <h2 class="font-semibold text-xl leading-tight">
                Create Email Template
            </h2>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
            <email-template-form
                :client-id="$page.props.user.client_id"
                :can-activate="false"
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

import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";

import EmailTemplateForm from "./Partials/EmailTemplateForm.vue";
import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";

export default defineComponent({
    props: {
        plansUrl: {
            type: String,
            required: true,
        },
        topolApiKey: {
            type: String,
            required: true,
        },
    },
    components: {
        LayoutHeader,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        EmailTemplateForm,
        ModalableWrapper,
        ModalSlot,
    },
    setup() {
        const handleClose = () => console.log("doclose");
        return { handleClose };
    },
});
</script>
