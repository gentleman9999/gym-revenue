<template>
    <LayoutHeader title="Create Security Role">
        <h2 class="font-semibold text-xl leading-tight">Create Department</h2>
    </LayoutHeader>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <department-form :client-id="$page.props.user.client_id" />
        </div>
    </div>
</template>

<script>
import { defineComponent } from "vue";
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import Button from "@/Components/Button.vue";
import JetFormSection from "@/Jetstream/FormSection.vue";
import JetInputError from "@/Jetstream/InputError.vue";
import JetLabel from "@/Jetstream/Label.vue";
import DepartmentForm from "@/Pages/Departments/Partials/DepartmentForm.vue";

export default defineComponent({
    components: {
        DepartmentForm,
        LayoutHeader,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
    },
    props: {},
});
</script>
