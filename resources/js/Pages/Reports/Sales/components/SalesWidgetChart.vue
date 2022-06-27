<template>
    <apex-charts
        type="donut"
        :height="height"
        :options="options"
        :series="data"
    ></apex-charts>
</template>
<script setup>
import ApexCharts from "vue3-apexcharts";

const props = defineProps({
    data: {
        type: Array,
        default: [54, 16, 23, 34],
    },
    height: {
        type: Number,
        default: 200,
    },
});

const options = {
    chart: {
        type: "donut",
    },
    labels: ["Calls", "Meetings", "Success", "Leads"],
    colors: [
        "hsl(var(--wa))",
        "hsl(var(--er))",
        "hsl(var(--in))",
        "var(--color-neutral-300)",
    ],
    legend: {
        show: false,
    },
    stroke: {
        show: false,
        width: 0,
    },
    dataLabels: {
        enabled: false,
    },
    plotOptions: {
        pie: {
            donut: {
                labels: {
                    show: true,
                    name: {
                        show: false,
                    },
                    value: {
                        show: true,
                        fontSize: "22px",
                        color: "hsl(var(--bc))",
                        fontWeight: 400,
                        offsetY: 8,
                        formatter: function (val) {
                            return val + "%";
                        },
                    },
                },
            },
        },
    },
};
</script>
