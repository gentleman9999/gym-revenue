<template>
    <div class="relative">
        <Button
            class="w-max"
            :size="size"
            secondary
            :outline="isCollapsed"
            :onClick="toggleCollapsed"
        >
            {{ label }}
            <select-box-icon :isCollapsed="isCollapsed" :size="size" />
        </Button>
        <select-box-content v-if="!isCollapsed">
            <select-box-item
                v-for="item in items"
                v-bind:key="item"
                :label="item"
                :selected="item === value"
                :onClick="onChange"
            />
        </select-box-content>
    </div>
</template>
<script setup>
import { ref } from "vue";
import Button from "@/Components/Button";
import SelectBoxIcon from "./SelectBoxIcon";
import SelectBoxContent from "./SelectBoxContent";
import SelectBoxItem from "./SelectBoxItem";

const props = defineProps({
    label: {
        type: String,
        default: "Select...",
    },
    items: {
        type: Array,
        default: [],
    },
    value: {
        type: String,
    },
    onChange: {
        type: Function,
        default: () => null,
    },
    size: {
        type: String,
        default: "sm",
    },
});
const isCollapsed = ref(true);
const toggleCollapsed = () => {
    isCollapsed.value = !isCollapsed.value;
};
const onChange = (value) => {
    toggleCollapsed();
    props.onChange(value);
};
</script>
