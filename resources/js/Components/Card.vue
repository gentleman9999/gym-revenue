<template>
    <div :class="derivedClass">
        <div
            v-if="label"
            class="absolute top-[-36px] text-sm font-bold rounded-t px-3 py-2 text-white bg-secondary"
        >
            {{ label }}
        </div>
        <slot></slot>
    </div>
</template>
<script setup>
import { computed } from "@vue/reactivity";
const props = defineProps({
    class: {
        type: String,
        default: "",
    },
    label: {
        type: String,
        default: "",
    },
});
const derivedClass = computed({
    get() {
        return `px-4 py-3 border border-secondary rounded-lg bg-neutral-900 relative ${
            props.class
        } ${props.label ? "mt-9" : ""}`;
    },
});
</script>
