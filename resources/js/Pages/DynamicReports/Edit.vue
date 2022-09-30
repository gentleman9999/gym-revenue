<template>
    <LayoutHeader title="Edit Dynamic Report">
        <jet-bar-icon type="g0back" fill />
        <h2 class="font-semibold text-xl leading-tight">Edit Dynamic Report</h2>
    </LayoutHeader>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <dynamic-report-form
                :client-id="$page.props.user.client_id"
                :report="report"
            />
        </div>
    </div>
</template>

<script setup>
import LayoutHeader from "@/Layouts/LayoutHeader.vue";
import JetBarIcon from "@/Components/JetBarIcon.vue";
import DynamicReportForm from "@/Pages/DynamicReports/Partials/DynamicReportForm.vue";

const props = defineProps({
    report: {
        type: Object,
        required: true,
    },
});
</script>
