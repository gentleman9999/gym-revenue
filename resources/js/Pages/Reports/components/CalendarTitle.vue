<template>
    <div
        class="flex flex-col md:flex-row w-full justify-between mt-[-14px] mb-[51px]"
    >
        <slot name="title"></slot>
        <div class="reports-title-filter">
            <Button size="xs" secondary> + Add Lead </Button>
            <Button outline size="xs" secondary> + Build a Campaign </Button>
            <Button outline size="xs" secondary> + Communication </Button>
            <search-input placeholder="Search" size="xs" />
        </div>
    </div>
</template>
<style scoped>
.reports-title-filter .btn-secondary {
    @apply text-base-content border-secondary rounded;
}
.reports-title-filter {
    max-width: calc(100vw - 32px);
    @apply flex flex-row items-center md:space-x-3 flex-wrap lg:flex-nowrap;
    > button,
    > div {
        @apply mb-2;
        margin-right: 8px !important;
        margin-left: 0px !important;
    }
}
</style>
<script setup>
import { ref } from "vue";
import SearchInput from "@/Components/SearchInput.vue";
import Button from "@/Components/Button.vue";

const props = defineProps({
    title: {
        type: String,
        default: "",
    },
});
</script>
