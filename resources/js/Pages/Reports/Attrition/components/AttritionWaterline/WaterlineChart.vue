<template>
    <apex-charts
        type="area"
        :height="250"
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
        type: "area",
        toolbar: {
            show: false,
        },
    },
    annotations: {
        xaxis: [
            {
                x: 0,
                strokeDashArray: 0,
                style: {
                    color: "var(--color-neutral)",
                },
            },
            {
                x: 7,
                strokeDashArray: 0,
                style: {
                    color: "var(--color-neutral)",
                },
            },
        ],
        yaxis: [
            {
                y: 0,
                strokeDashArray: 0,
                style: {
                    color: "var(--color-neutral)",
                },
            },
            {
                y: 150,
                strokeDashArray: 0,
                style: {
                    color: "var(--color-neutral)",
                },
            },
        ],
    },
    colors: ["hsl(var(--p))", "hsl(var(--pf))"],
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
        show: true,
        markers: {
            height: 20,
            radius: 0,
            width: 20,
        },
        labels: {
            colors: "hsl(var(--bc))",
        },
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
        axisBorder: {
            show: false,
        },
        axisTicks: {
            show: false,
        },
        labels: {
            style: {
                colors: new Array(7).fill("transparent"),
            },
        },
    },
    yaxis: {
        min: 0,
        max: 150,
        tickAmount: 3,
        labels: {
            show: false,
        },
    },
};
</script>
