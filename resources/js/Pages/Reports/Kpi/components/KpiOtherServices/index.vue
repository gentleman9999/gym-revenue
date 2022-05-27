<template>
    <kpi-card-wrapper class="w-full">
        <kpi-card
            class="kpi-card w-full"
            header="Personal Training or Other Add-on Services"
        >
            <div class="flex flex-row flex-wrap">
                <attachment-rate />
                <service-summary />
                <penetration-percent />
            </div>
        </kpi-card>
    </kpi-card-wrapper>
</template>
<script setup>
import KpiCardWrapper from "../KpiCardWrapper";
import KpiCard from "@/Components/Card";
import AttachmentRate from "./AttachmentRate";
import ServiceSummary from "./ServiceSummary";
import PenetrationPercent from "./PenetrationPercent";
</script>
