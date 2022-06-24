<template>
    <select-box :items="items" :label="label" :onChange="handleChange" />
</template>
<script setup>
import { ref } from "vue";
import SelectBox from "@/Components/SelectBox";
const items = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
];

const props = defineProps({
    onChange: {
        type: Function,
        default: () => null,
    },
});
const label = ref("Month");
const handleChange = (value) => {
    let ndx = items.indexOf(value);
    label.value = value;
    props.onChange(ndx);
};
</script>
