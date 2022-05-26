<template>
    <span :class="derivedClass" @click="isFavorite = !isFavorite">&#9733;</span>
</template>
<style scoped></style>
<script setup>
import { computed } from "@vue/reactivity";
const props = defineProps({
    value: {
        type: Boolean,
        default: true,
    },
    onChange: {
        type: Function,
        default: () => null,
    },
});
const isFavorite = computed({
    get() {
        return props.value;
    },
    set(value) {
        props.onChange(value);
    },
});
const derivedClass = computed({
    get() {
        return props.value
            ? "text-2xl text-secondary"
            : "text-2xl text-neutral-500";
    },
});
</script>
