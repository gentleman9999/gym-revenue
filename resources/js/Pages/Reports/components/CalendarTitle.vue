<template>
    <div
        class="flex flex-col md:flex-row w-full justify-between mt-[-14px] mb-[51px]"
    >
        <slot name="title"></slot>
        <div
            class="reports-title-filter flex flex-row items-center space-x-3 flex-wrap lg:flex-nowrap"
        >
            <Button size="xs" secondary> + Add Lead </Button>
            <Button outline size="xs" secondary> + Build a Campaign </Button>
            <Button outline size="xs" secondary> + Communication </Button>
            <search-input placeholder="Search" size="xs" />
        </div>
    </div>
</template>
<style scope>
.reports-title-filter .btn-secondary {
    @apply text-base-content border-secondary rounded;
}
.reports-title-filter {
    max-width: calc(100vw - 32px);
}
</style>
<script setup>
import { ref } from "vue";
import SearchInput from "@/Components/SearchInput";
import Button from "@/Components/Button";

const props = defineProps({
    title: {
        type: String,
        default: "",
    },
});
</script>
