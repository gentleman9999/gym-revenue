<template>
    <table :class="'overflow-scroll block max-w-[90vw] ' + props.class">
        <table-header :columns="columns" />
        <table-body :columns="columns" :data="data" />
    </table>
</template>
<script setup>
import TableHeader from "./TableHeader";
import TableBody from "./TableBody";
const props = defineProps({
    class: {
        type: String,
        default: "",
    },
    data: {
        type: Array,
        default: [],
    },
    columns: {
        type: Array,
        default: [],
    },
});
</script>
