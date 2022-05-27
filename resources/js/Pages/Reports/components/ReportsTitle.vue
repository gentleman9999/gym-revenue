<template>
    <div class="flex flex-row w-full justify-between mt-[-14px] mb-[51px]">
        <slot name="title"></slot>
        <div class="reports-title-filter flex flex-row items-center space-x-3">
            <select-box
                label="Data View"
                size="xs"
                :items="viewOptions"
                :value="viewMode"
                :onChange="setViewMode"
            />
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
import { ref } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import SearchInput from "@/Components/SearchInput";
import Button from "@/Components/Button";
import SelectBox from "@/Components/SelectBox";
const props = defineProps({
    title: {
        type: String,
        default: "",
    },
});
const viewOptions = ["Yesterday", "Today", "Week", "MTD"];

const viewMode = ref("");
const setViewMode = (value) => {
    viewMode.value = value;
};
</script>
