<template>
    <apex-charts
        type="radialBar"
        :height="height"
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
    height: {
        type: Number,
        default: 200,
    },
});

const options = {
    chart: {
        type: "radialBar",
    },
    plotOptions: {
        radialBar: {
            inverseOrder: false,
            startAngle: 0,
            endAngle: 360,
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
    stroke: {
        lineCap: "round",
    },
    grid: {
        show: true,
    },
    colors: ["hsl(var(--s))"],
    labels: [""],
};
</script>
