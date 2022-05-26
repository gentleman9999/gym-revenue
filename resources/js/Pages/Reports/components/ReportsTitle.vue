<template>
    <div class="flex flex-row w-full justify-between">
        <slot name="title"></slot>
        <div class="reports-title-filter flex flex-row items-center space-x-3">
            <Button size="xs" secondary outline> Data View </Button>
            <Button size="xs" secondary> + Add Lead </Button>
            <Button outline size="xs" secondary> + Build a Campaign </Button>
            <Button outline size="xs" secondary> + Communication </Button>
            <search-input placeholder="Search" size="xs" />
        </div>
    </div>
</template>
<style scope>
.reports-title-filter .btn-secondary {
    color: #fff;
    border-radius: 4px;
    border-color: #0074c8;
}
</style>
<script setup>
import SearchInput from "@/Components/SearchInput";
import ArrowIcon from "@/Components/Icons/Arrow";
import Button from "@/Components/Button";
const props = defineProps({
    title: {
        type: String,
        default: "",
    },
});
</script>
