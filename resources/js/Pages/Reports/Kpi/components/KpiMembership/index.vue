<template>
    <kpi-card-wrapper class="lg:w-2/3">
        <kpi-card
            class="kpi-card w-full"
            header="Membership"
            :options="{
                collapse: true,
                favorite: true,
            }"
        >
            <div class="w-full">
                <!-- <retail-chart /> -->
            </div>
        </kpi-card>
    </kpi-card-wrapper>
</template>
<script setup>
import { ref } from "vue";
import KpiCard from "@/Components/Card.vue";
import KpiCardWrapper from "../KpiCardWrapper.vue";
</script>
