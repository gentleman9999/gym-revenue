<template>
    <div :class="'overflow-y-scroll max-w-[90vw] pr-2 ' + props.class">
        <table
            :class="{
                'border-separate': rowBordered,
            }"
        >
            <table-header :columns="columns" :rowBordered="rowBordered" />
            <table-body
                :columns="columns"
                :data="data"
                :collapsed="collapsed"
                :border="borderType"
                :rowBordered="rowBordered"
            />
        </table>
    </div>
</template>
<style scoped>
table {
    width: 100%;
    border-spacing: 0 5px;
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
    borderType: {
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
    rowBordered: {
        type: Boolean,
        default: false,
    },
});
</script>
