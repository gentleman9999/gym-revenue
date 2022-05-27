<template>
    <table :class="'overflow-y-scroll block ' + props.class">
        <table-header :columns="columns" />
        <table-body :columns="columns" :data="data" />
    </table>
</template>
<style scoped>
table::-webkit-scrollbar {
    width: 0.5em;
}

table::-webkit-scrollbar-track {
    border: 1px solid #0074c8;
    border-radius: 10px;
}

table::-webkit-scrollbar-thumb {
    background-color: #d9d9d9;
    border-color: #707070;
    border-radius: 10px;
}
</style>
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
