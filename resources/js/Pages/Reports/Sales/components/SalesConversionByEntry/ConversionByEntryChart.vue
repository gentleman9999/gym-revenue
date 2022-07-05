<template>
    <apex-charts :options="options" :series="data"></apex-charts>
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
        type: "bar",
        toolbar: {
            show: false,
        },
    },
    plotOptions: {
        bar: {
            horizontal: true,
        },
    },
    dataLabels: {
        formatter: function (val) {
            return val + "%";
        },
        style: {
            colors: ["hsl(var(--bc))"],
        },
        textAnchor: "start",
    },
    xaxis: {
        tickAmount: 2,
        max: 100,
        categories: [
            "Walk-In",
            "ABC Join Pros…",
            "Hand2Hand",
            "Telephone",
            "Internal Table",
            "Free Pass Web",
            "Buddy Referra…",
            "Limecard Guest",
            "B2B Prospecti…",
            "Corporate Ons…",
        ],
        labels: {
            style: {
                colors: ["hsl(var(--bc))"],
            },
            formatter: function (val) {
                return val + "%";
            },
        },
        axisBorder: {
            color: "hsl(var(--s))",
        },
        axisTicks: {
            show: false,
        },
    },
    yaxis: {
        labels: {
            style: {
                colors: new Array(10).fill("hsl(var(--bc))"),
            },
        },
    },
    annotations: {
        xaxis: [
            {
                x: 100,
                strokeDashArray: 0,
                borderColor: "hsl(var(--s))",
            },
        ],
        yaxis: [
            {
                y: 0,
                strokeDashArray: 0,
                borderColor: "hsl(var(--s))",
            },
        ],
    },
    colors: ["hsl(var(--s))"],
    grid: {
        show: false,
    },
};
</script>
