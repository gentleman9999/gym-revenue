<template>
    <div :class="className">
        <Button
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
<style>
.select-box-wrapper {
    position: relative;
}
.select-box-wrapper.bg-white.collapsed:not(:hover) button {
    color: black;
}
.select-box-wrapper.bg-white.collapsed:not(:hover) svg {
    filter: brightness(0);
}
</style>
<script setup>
import { ref, computed } from "vue";
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
    class: {
        type: String,
        default: "",
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

const className = computed({
    get() {
        let additional = isCollapsed.value ? " collapsed" : "";
        return "select-box-wrapper " + props.class + additional;
    },
});
</script>
