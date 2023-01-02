<template>
    <LayoutHeader title="Edit Email Template">
        <h2 class="font-semibold text-xl leading-tight">Edit Email Template</h2>
    </LayoutHeader>

    <email-template-form
        :client-id="$page.props.user.client_id"
        :template="template"
        :can-activate="true"
        :topol-api-key="topolApiKey"
    />
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import EmailTemplateForm from "./Partials/EmailTemplateForm.vue";

const props = defineProps({
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
});
</script>
