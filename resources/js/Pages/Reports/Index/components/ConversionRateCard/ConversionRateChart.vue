<template>
    <apex-charts
        type="bar"
        :options="options"
        :series="data"
        ref="chartContainer"
    ></apex-charts>
</template>
<script setup>
import { ref } from "vue";
import ApexCharts from "vue3-apexcharts";

// const props = defineProps({
//     data: {
//         type: Array,
//         default: [],
//     },
// });

const data = [
    {
        name: "conversion",
        data: [
            {
                x: "Pre-roll",
                y: 30,
            },
            {
                x: "Mid-roll",
                y: 30,
            },
            {
                x: "Post-roll",
                y: 45,
            },
        ],
    },
    {
        name: "rolling",
        data: [
            {
                x: "Pre-roll",
                y: 15,
            },
            {
                x: "Mid-roll",
                y: 30,
            },
            {
                x: "Post-roll",
                y: 2,
            },
        ],
    },
];
const options = {
    chart: {
        type: "bar",
        toolbar: {
            show: false,
        },
    },
    yaxis: [
        {
            min: 0,
            max: 60,
            tickAmount: 4,
            labels: {
                style: {
                    colors: ["hsl(var(--bc))"],
                },
                formatter: function (val) {
                    return val + "%";
                },
            },
            axisBorder: {
                show: true,
            },
        },
        {
            opposite: true,
            axisBorder: {
                show: true,
            },
            labels: {
                show: false,
            },
        },
    ],
    xaxis: {
        axisTicks: {
            show: false,
        },
        labels: {
            style: {
                colors: ["hsl(var(--bc))", "hsl(var(--bc))", "hsl(var(--bc))"],
            },
        },
    },
    legend: {
        show: false,
    },
    grid: {
        show: false,
    },
    dataLabels: {
        enabled: false,
    },
    annotations: {
        yaxis: [
            {
                strokeDashArray: 0,
                y: 60,
            },
        ],
    },
    stroke: {
        colors: ["transparent"],
        width: 5,
    },
    plotOptions: {
        bar: {
            columnWidth: 60,
        },
    },
    colors: ["hsl(var(--p))", "hsl(var(--pf))"],
};
</script>
