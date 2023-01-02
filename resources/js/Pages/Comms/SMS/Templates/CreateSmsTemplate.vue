<template>
    <LayoutHeader title="Create SMS Template">
        <h2 class="font-semibold text-xl leading-tight">Create SMS Template</h2>
    </LayoutHeader>

    <sms-template-form
        :client-id="$page.props.user.client_id"
        :can-activate="false"
    />
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import SmsTemplateForm from "./Partials/SmsTemplateForm.vue";
</script>
