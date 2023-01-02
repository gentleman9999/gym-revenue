<template>
    <LayoutHeader title="Edit SMS Template">
        <h2 class="font-semibold text-xl leading-tight">Edit SMS Template</h2>
    </LayoutHeader>

    <sms-template-form
        :client-id="$page.props.user.client_id"
        :template="template"
        :can-activate="true"
    />
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import SmsTemplateForm from "./Partials/SmsTemplateForm.vue";

const props = defineProps({
    template: {
        required: true,
        type: Object,
    },
});
</script>
