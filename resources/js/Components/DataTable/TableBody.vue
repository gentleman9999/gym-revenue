<template>
    <tbody>
        <tr v-for="item in data" :key="item.id">
            <td
                v-for="(column, col_ndx) in columns"
                :key="column.field + item.id"
                class="border-b"
                :class="{
                    'border-secondary': border === 'secondary',
                    'border-neutral-450': border !== 'secondary',
                    'border-l': col_ndx === 0 && rowBordered,
                    'border-t': rowBordered,
                    'border-r': col_ndx === columns.length - 1 && rowBordered,
                }"
            >
                <div
                    class="h-full"
                    :class="{
                        'border-r':
                            !collapsed &&
                            !column.noSeparator &&
                            col_ndx !== columns.length - 1,
                        'border-secondary': border === 'secondary',
                        'border-neutral-450': border !== 'secondary',
                    }"
                >
                    <table-cell
                        :value="item[column.field]"
                        :renderer="column.renderer ? column.renderer : null"
                    />
                </div>
            </td>
        </tr>
    </tbody>
</template>
<style scoped>
td {
    height: 1px;
    padding: 12px 0;
    /* border-color: #868686; */
}
td > div {
    text-align: center;
}
</style>
<script setup>
import { h } from "vue";
import TableCell from "./TableCell";
const props = defineProps({
    border: {
        type: String,
        default: "",
    },
    columns: {
        type: Array,
        default: [],
    },
    data: {
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
