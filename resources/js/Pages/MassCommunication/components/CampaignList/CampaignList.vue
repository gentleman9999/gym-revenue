<template>
    <data-table
        :columns="columns"
        :data="filteredCampaigns"
        borderType="secondary"
        class="campaign-list"
    />
</template>
<style scoped>
.campaign-list {
    @apply h-80;
}
@media only screen and (max-width: 768px) {
    .campaign-list {
        width: calc(100vw - 64px);
    }
}
</style>
<script setup>
import { computed, h } from "vue";
import DataTable from "@/Components/DataTable";
import CampaignActions from "./CampaignActions.vue";
import CampaignStatus from "./CampaignStatus.vue";
import DeploymentDays from "./DeploymentDays.vue";
import { BarChartIcon } from "@/Components/Icons";
import Button from "@/Components/Button.vue";
import { filterCampaigns } from "@/Pages/MassCommunication/components/Creator/helpers";

const props = defineProps({
    campaigns: {
        type: Array,
        required: true,
    },
    type: {
        type: String,
        required: true,
    },
    filter: {
        type: String,
    },
});

const emit = defineEmits("open-campaign");

const dripColumns = [
    {
        field: "days",
        label: "Actions",
        //sorry about this line. if it breaks just ask philip to fix it lol.
        renderer: (days) =>
            h(CampaignActions, {
                actions: Object.entries(
                    days
                        ?.map(
                            ({
                                email_template_id,
                                sms_template_id,
                                call_template_id,
                            }) => ({
                                email: !!email_template_id,
                                sms: !!sms_template_id,
                                call: !!call_template_id,
                            })
                        )
                        .reduce((acc, obj) => ({
                            call: acc.call || obj.call,
                            sms: acc.sms || obj.sms,
                            email: acc.email || obj.email,
                        }))
                )
                    .filter(([key, val]) => !!val)
                    .map(([key]) => key),
            }),
    },
    {
        field: "name",
        label: "Name",
    },
    {
        field: "daysCount",
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
        renderer: (value) =>
            h(
                Button,
                {
                    size: "xs",
                    secondary: true,
                    onClick: () =>
                        emit("open-campaign", {
                            campaign: value,
                            type: "drip",
                        }),
                },
                "View"
            ),
    },
    {
        label: "Report",
        renderer: (value) => h(BarChartIcon, { class: "text-accent" }),
    },
];

const scheduledColumns = [
    {
        label: "Actions",
        //sorry about this line. if it breaks just ask philip to fix it lol.
        renderer: ({ email_template_id, sms_template_id, call_template_id }) =>
            h(CampaignActions, {
                actions: Object.entries({
                    email: !!email_template_id,
                    sms: !!sms_template_id,
                    call: !!call_template_id,
                })
                    .filter(([key, val]) => !!val)
                    .map(([key]) => key),
            }),
    },
    {
        field: "name",
        label: "Name",
    },
    {
        field: "status",
        label: "Status",
        renderer: (value) => h(CampaignStatus, { value }),
    },
    {
        label: "Preview",
        renderer: (value) =>
            h(
                Button,
                {
                    size: "xs",
                    secondary: true,
                    onClick: () =>
                        emit("open-campaign", {
                            campaign: value,
                            type: "scheduled",
                        }),
                },
                "View"
            ),
    },
    {
        label: "Report",
        renderer: (value) => h(BarChartIcon, { class: "text-accent" }),
    },
];

// const columns = computed(() =>
//     props.type === "drip" ? dripColumns : scheduledColumns
// );

const columns = computed(() => {
    if (props.type === "drip" || props.type === "DripCampaign")
        return dripColumns;
    else if (props.type === "scheduled") return scheduledColumns;
    else return [];
});

const filteredCampaigns = computed(() =>
    filterCampaigns(props.campaigns, props.filter)
);
</script>
