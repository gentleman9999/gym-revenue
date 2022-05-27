<template>
    <tbody>
        <tr v-for="item in data" :key="item.id">
            <td
                v-for="column in columns"
                :key="column.field + item.id"
                class="border-b"
            >
                <div class="border-r h-full">
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
    border-color: #868686;
}
td > div {
    border-color: #868686;
    text-align: center;
}
</style>
<script setup>
import { h } from "vue";
import TableCell from "./TableCell";
const props = defineProps({
    columns: {
        type: Array,
        default: [],
    },
    data: {
        type: Array,
        default: [],
    },
});
</script>
