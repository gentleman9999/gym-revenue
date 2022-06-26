<template>
    <div class="bg-secondary border border-2 rounded-lg">
        <apex-charts
            type="bar"
            :options="options"
            :series="data"
            ref="chartContainer"
        ></apex-charts>
    </div>
</template>
<script setup>
import { ref } from "vue";
import ApexCharts from "vue3-apexcharts";

// const props = defineProps({
//     data: {
//         type: Array,
//         default: [
//             75000,
//             80000,
//             85000,
//             90000,
//             100000,
//             110000,
//             120000,
//         ],
//     },
// });

const data = [
    {
        data: [75000, 80000, 85000, 90000, 100000, 110000, 120000],
    },
];
const chartContainer = ref();

const options = {
    chart: {
        type: "bar",
        toolbar: {
            show: false,
        },
    },
    colors: ["hsl(var(--s))"],
    legend: {
        show: false,
    },
    dataLabels: {
        enabled: false,
    },
    grid: {
        show: false, // you can either change hear to disable all grids
    },
    fill: {
        colors: "hsl(var(--bc))",
        opacity: 1,
    },
    tooltip: {
        y: {
            formatter: function (val) {
                return "$ " + val + " thousands";
            },
        },
    },
    yaxis: {
        min: 70000,
        grid: false,
        labels: {
            style: {
                colors: "hsl(var(--bc))",
            },
        },
        axisBorder: {
            show: true,
            color: "hsl(var(--bc))",
        },
    },
    xaxis: {
        axisBorder: {
            show: false,
        },
        axisTicks: {
            show: false,
        },
        tickAmount: 6,
        grid: false,
        labels: {
            style: {
                colors: "hsl(var(--bc))",
            },
            rotate: 0,
        },
        tooltip: { enabled: false },
    },
    plotOptions: {
        bar: {
            borderRadius: 5,
            columnWidth: 20,
        },
    },
};
</script>
