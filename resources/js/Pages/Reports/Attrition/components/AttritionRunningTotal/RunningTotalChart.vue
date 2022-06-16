<template>
    <apex-charts
        type="area"
        :height="200"
        :options="options"
        :series="data"
    ></apex-charts>
</template>
<script setup>
import ApexCharts from "vue3-apexcharts";

const props = defineProps({
    data: {
        type: Array,
        default: [],
    },
});

const options = {
    chart: {
        type: "spline",
        toolbar: {
            show: false,
        },
    },
    annotations: {
        xaxis: [
            {
                x: 1,
                strokeDashArray: 0,
                style: {
                    color: "var(--color-neutral-500)",
                },
            },
            {
                x: 3,
                strokeDashArray: 0,
                style: {
                    color: "var(--color-neutral-500)",
                },
            },
            {
                x: 5,
                strokeDashArray: 0,
                style: {
                    color: "var(--color-neutral-500)",
                },
            },
            {
                x: 7,
                strokeDashArray: 0,
                style: {
                    color: "var(--color-neutral-500)",
                },
            },
        ],
        yaxis: [
            {
                y: 0,
                strokeDashArray: 0,
                style: {
                    color: "var(--color-neutral-500)",
                },
            },
            {
                y: 120,
                strokeDashArray: 0,
                style: {
                    color: "var(--color-neutral-500)",
                },
            },
        ],
    },
    colors: ["hsl(var(--su))"],
    dataLabels: {
        enabled: false,
    },
    fill: {
        opacity: 0.1,
        type: "gradient",
        gradient: {
            shadeIntensity: 1,
            inverseColors: false,
            opacityFrom: 0,
            opacityTo: 0,
            stops: [20, 100, 100, 100],
        },
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
        min: 1,
        max: 7,
        grid: false,
        tooltip: { enabled: false },
        tickAmount: 3,
        axisBorder: {
            show: false,
        },
        axisTicks: {
            show: false,
        },
    },
    yaxis: {
        tickAmount: 3,
        labels: {
            style: {
                colors: "hsl(var(--bc))",
            },
        },
    },
};
</script>
