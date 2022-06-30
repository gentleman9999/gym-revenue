<template>
    <apex-charts
        type="radialBar"
        height="350"
        :options="options"
        :series="data"
    ></apex-charts>
</template>
<script setup>
import ApexCharts from "vue3-apexcharts";
const props = defineProps({
    data: {
        type: Array,
        default: [65],
    },
});
const options = {
    chart: {
        type: "radialBar",
        offsetY: -20,
        sparkline: {
            enabled: true,
        },
    },
    plotOptions: {
        radialBar: {
            startAngle: -90,
            endAngle: 90,
            track: {
                // background: "hsl(var(--b1))",
                strokeWidth: "100%",
                margin: 5, // margin is in pixels
                dropShadow: {
                    enabled: false,
                },
            },
            dataLabels: {
                name: {
                    show: false,
                },
                value: {
                    offsetY: -2,
                    fontSize: "22px",
                    color: "hsl(var(--bc))",
                },
            },
        },
    },
    colors: ["hsl(var(--s))"],
    labels: ["Average Results"],
};
</script>
