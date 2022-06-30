<template>
    <div :class="className">
        <Button
            :size="size"
            secondary
            :outline="isCollapsed"
            :onClick="toggleCollapsed"
        >
            {{ label }}
            <select-box-icon
                :isCollapsed="isCollapsed"
                :size="size"
                :color="color"
            />
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
    @apply relative w-fit;
}
</style>
<script setup>
import { ref, computed } from "vue";
import Button from "@/Components/Button.vue";
import SelectBoxIcon from "./SelectBoxIcon.vue";
import SelectBoxContent from "./SelectBoxContent.vue";
import SelectBoxItem from "./SelectBoxItem.vue";

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
    color: {
        type: String,
        default: "text-base-content",
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
