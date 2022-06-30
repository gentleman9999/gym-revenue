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
                :client-id="$page.props.user.current_client_id"
                :can-activate="false"
                :topol-api-key="topolApiKey"
            />
            <!--            <email-builder @close="handleClose" :products-url="plansUrl" />-->
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

import EmailTemplateForm from "./Partials/EmailTemplateForm";
import EmailBuilder from "./Partials/EmailBuilder";
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
        EmailBuilder,
    },
    setup() {
        const handleClose = () => console.log("doclose");
        return { handleClose };
    },
});
</script>
