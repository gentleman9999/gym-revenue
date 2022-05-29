<template>
    <table :class="'overflow-y-scroll block max-w-[90vw] ' + props.class">
        <table-header :columns="columns" />
        <table-body :columns="columns" :data="data" :collapsed="collapsed" />
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
    collapsed: {
        type: Boolean,
        default: false,
    },
});
</script>
