<template>
    <card>
        <template #cardTitle>
            <div class="text-lg text-base-content">Projecting</div>
        </template>
        <div class="flex flex-col">
            <div class="flex flex-wrap text-sm">
                Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed
                diam nonumy eirmod tempor
            </div>
            <div class="w-fit">
                <projecting-chart :data="data" :height="250" />
            </div>
        </div>
    </card>
</template>

<script setup>
import Card from "@/Components/Card";
import ProjectingChart from "./ProjectingChart";

const data = [82, 70, 75, 40, 25];
</script>
