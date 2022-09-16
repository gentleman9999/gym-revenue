<template>
    <LayoutHeader title="Create Dynamic Report">
        <h2 class="font-semibold text-xl leading-tight">Create Report</h2>
    </LayoutHeader>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <dynamic-report-form
                :client-id="$page.props.user.client_id"
                :reports="reports"
            />
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
import DynamicReportForm from "@/Pages/DynamicReports/Partials/DynamicReportForm.vue";

export default defineComponent({
    components: {
        DynamicReportForm,
        LayoutHeader,
        Button,
        JetFormSection,
        JetInputError,
        JetLabel,
    },
    props: {
        reports: {
            type: Object,
            required: true,
        },
    },
});
</script>
