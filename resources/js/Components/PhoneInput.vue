<template>
    <input type="tel" v-model="localValue" v-bind="$attrs" />
</template>
<style scoped>
input[type="tel"] {
    @apply w-full mt-1;
}
</style>
<script setup>
import { defineProps, computed, defineEmits } from "vue";

const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },
});
const emit = defineEmits(["update:modelValue"]);
const localValue = computed({
    get: () => {
        let modelValue = props.modelValue ? props.modelValue : "";
        let x = modelValue
            .replace(/\D/g, "")
            .match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
        return !x[2]
            ? x[1]
            : "(" + x[1] + ") " + x[2] + (x[3] ? "-" + x[3] : "");
    },
    set: (value) => {
        let x = value.replace(/\D/g, "").match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
        emit("update:modelValue", x[0]);
    },
});
</script>
