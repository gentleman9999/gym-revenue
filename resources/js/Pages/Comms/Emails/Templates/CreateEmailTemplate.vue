<template>
    <LayoutHeader title="Create Email Template">
        <h2 class="font-semibold text-xl leading-tight">
            Create Email Template
        </h2>
    </LayoutHeader>

    <email-template-form
        :client-id="$page.props.user.client_id"
        :can-activate="false"
        :topol-api-key="topolApiKey"
    />
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import EmailTemplateForm from "./Partials/EmailTemplateForm.vue";

const props = defineProps({
    plansUrl: {
        type: String,
        required: true,
    },
    topolApiKey: {
        type: String,
        required: true,
    },
});
</script>
