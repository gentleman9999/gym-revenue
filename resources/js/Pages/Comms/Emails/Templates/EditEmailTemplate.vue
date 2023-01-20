<template>
    <LayoutHeader title="Edit Email Template">
        <h2 class="font-semibold text-xl leading-tight">Edit Email Template</h2>
    </LayoutHeader>

    <email-template-form :editParam="editParam" />
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import EmailTemplateForm from "./Partials/EmailTemplateForm.vue";

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
});

const editParam = ref({
    id: props.id,
});
</script>
