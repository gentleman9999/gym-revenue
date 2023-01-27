<template>
    <ModalableWrapper>
        <LayoutHeader title="Edit Call Script Template">
            <h2 class="font-semibold text-xl leading-tight">
                Edit Call Script Template
            </h2>
        </LayoutHeader>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <ModalSlot />
            </div>
        </div>
        <template #modal>
            <call-script-template-form
                :client-id="$page.props.user.client_id"
                :template="template"
                :can-activate="true"
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

import CallScriptTemplateForm from "./Partials/CallScriptTemplateForm.vue";

import { ModalableWrapper, ModalSlot } from "@/Components/InertiaModal";

export default defineComponent({
    components: {
        LayoutHeader,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
        CallScriptTemplateForm,
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
