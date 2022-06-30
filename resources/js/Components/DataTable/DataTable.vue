<template>
    <div :class="'overflow-y-auto max-w-[90vw] pr-2 ' + props.class">
        <table
            :class="{
                'border-separate': borderSeprated,
            }"
        >
            <table-header
                v-if="!noHeader"
                :columns="columns"
                :rowBordered="rowBordered"
            />
            <slot name="thead"></slot>
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
import TableHeader from "./TableHeader.vue";
import TableBody from "./TableBody.vue";
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
    borderSeprated: {
        type: Boolean,
        default: true,
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
