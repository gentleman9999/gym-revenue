<template>
    <apex-charts
        type="radialBar"
        height="200"
        :options="options"
        :series="props.data"
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
        type: "radialBar",
    },
    plotOptions: {
        radialBar: {
            hollow: {
                size: "40%",
                color: "white",
            },
            dataLabels: {
                name: {
                    show: false,
                },
                value: {
                    offsetY: 10,
                    fontSize: "22px",
                    color: "hsl(var(--bc))",
                },
            },
        },
    },
    grid: {
        show: true,
        padding: {
            left: -25,
            right: -25,
            top: -25,
            bottom: -25,
        },
    },
    colors: ["hsl(var(--s))"],
    labels: [""],
};
</script>
