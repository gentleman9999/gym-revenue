<template>
    <data-table
        :columns="columns"
        :data="data"
        borderType="secondary"
        class="h-80"
    />
</template>
<script setup>
import { h } from "vue";
import DataTable from "@/Components/DataTable";
import CampaignAction from "./CampaignAction.vue";
import CampaignStatus from "./CampaignStatus.vue";
import DeploymentDays from "./DeploymentDays.vue";
import { BarChartIcon } from "@/Components/Icons";
import Button from "@/Components/Button.vue";

const columns = [
    {
        field: "action",
        label: "Actions",
        renderer: (value) => h(CampaignAction, { action: value }),
    },
    {
        field: "subject",
        label: "Subject",
    },
    {
        field: "deployment_days",
        label: "Deployment Days",
        renderer: (value) => h(DeploymentDays, { value }),
    },
    {
        field: "status",
        label: "Status",
        renderer: (value) => h(CampaignStatus, { value }),
    },
    {
        label: "Preview",
        renderer: (value) => h(Button, { size: "xs", secondary: true }, "View"),
    },
    {
        label: "Report",
        renderer: (value) => h(BarChartIcon, { class: "text-accent" }),
    },
];

const data = [
    {
        id: 1,
        action: "sms",
        subject: "Welcome Mail",
        deployment_days: 2,
        status: "In Progress",
    },
    {
        id: 2,
        action: "chat",
        subject: "Introduction",
        deployment_days: 23,
        status: "Pause",
    },
    {
        id: 3,
        action: "phone",
        subject: "Follow Up",
        deployment_days: 67,
        status: "Rescheduled",
    },
    {
        id: 4,
        action: "todo",
        subject: "PT Task",
        deployment_days: 2,
        status: "Completed",
    },
    {
        id: 5,
        action: "chat",
        subject: "Q/A",
        deployment_days: 12,
        status: "In Progress",
    },
    {
        id: 6,
        action: "sms",
        subject: "Membership Offer",
        deployment_days: 4,
        status: "In Progress",
    },
];
</script>
