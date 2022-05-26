<template>
    <span
        :class="{
            'favorite-btn text-2xl': true,
            'text-secondary': value,
            'text-neutral-500': !value,
        }"
        @click="isFavorite = !isFavorite"
    >
        &#9733;
    </span>
</template>
<style scoped>
.favorite-btn {
    user-select: none;
}
</style>
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
</script>
