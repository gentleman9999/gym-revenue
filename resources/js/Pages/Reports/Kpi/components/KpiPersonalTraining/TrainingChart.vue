<template>
    <div class="bg-base-content rounded-lg">
        <apex-charts
            type="area"
            :height="250"
            :options="options"
            :series="mock"
        ></apex-charts>
    </div>
</template>
<script setup>
import ApexCharts from "vue3-apexcharts";

const props = defineProps({
    data: {
        type: Array,
        default: [],
    },
});

const mock = [
    {
        name: "New Members",
        data: [65, 80, 70, 150, 90, 210, 180],
    },
    {
        name: "Renewals",
        data: [20, 65, 250, 80, 150, 120, 90],
    },
];

const options = {
    chart: {
        type: "area",
        toolbar: {
            show: false,
        },
    },
    colors: ["var(--color-secondary-500)", "var(--color-secondary-700)"],
    fill: {
        type: "gradient",
        opacity: 0.7,
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.7,
            opacityTo: 0.7,
            stops: [100, 100, 100],
        },
    },
    dataLabels: {
        enabled: false,
    },
    grid: {
        show: false,
    },
    legend: {
        show: false,
    },
    stroke: {
        curve: "smooth",
        width: 1,
    },
    xaxis: {
        grid: false,
        tooltip: { enabled: false },
        axisBorder: {
            show: false,
        },
        axisTicks: {
            show: false,
        },
        labels: {
            show: false,
        },
    },
    yaxis: {
        min: 0,
        max: 250,
        tickAmount: 5,
        labels: {
            show: true,
        },
    },
};
</script>
