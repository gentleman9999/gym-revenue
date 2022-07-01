<template>
    <kpi-card-wrapper class="w-full">
        <kpi-card
            class="kpi-card w-full"
            header="Personal Training or Other Add-on Services"
        >
            <div class="flex flex-row flex-wrap">
                <attachment-rate class="no-more" />
                <service-summary />
                <penetration-percent />
            </div>
        </kpi-card>
    </kpi-card-wrapper>
</template>
<script setup>
import KpiCardWrapper from "../KpiCardWrapper.vue";
import KpiCard from "@/Components/Card.vue";
import AttachmentRate from "./AttachmentRate.vue";
import ServiceSummary from "./ServiceSummary.vue";
import PenetrationPercent from "./PenetrationPercent.vue";
</script>
