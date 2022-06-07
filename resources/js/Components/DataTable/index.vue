<template>
    <div :class="'overflow-y-auto max-w-[90vw] pr-2 ' + props.class">
        <table
            :class="{
                'border-separate': rowBordered,
            }"
        >
            <table-header
                v-if="!noHeader"
                :columns="columns"
                :rowBordered="rowBordered"
            />
            <table-body
                :columns="columns"
                :data="data"
                :collapsed="collapsed"
                :border="borderType"
                :rowBordered="rowBordered"
                :interactive="interactive"
            />
        </table>
    </div>
</template>
<style scoped>
table {
    border-spacing: 0 5px;
    @apply w-full;
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

    borderType: {
        type: String,
        default: "",
    },
    collapsed: {
        type: Boolean,
        default: false,
    },
    rowBordered: {
        type: Boolean,
        default: false,
    },
    interactive: {
        type: Boolean,
        default: false,
    },
    noHeader: {
        type: Boolean,
        default: false,
    },
});
</script>
