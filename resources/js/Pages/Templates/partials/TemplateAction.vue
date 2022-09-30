<template>
    <button class="group" :disabled="disabled" type="button" @click="handler">
        <slot name="icon" />
        <p
            class="group-hover:-top-8 group-hover:scale-90 group-hover:opacity-100"
        >
            <slot name="text" />
        </p>
    </button>
</template>

<style scoped>
button {
    @apply p-2 mx-2 scale-100 transition-all duration-300 ease-in-out relative;
    @apply active:scale-95 active:opacity-50;
    @apply hover:scale-125;
    @apply disabled:opacity-25 disabled:cursor-not-allowed;
}

button:active {
    @apply opacity-50 transition-opacity ease-out;
}

button p {
    @apply absolute py-0 w-16 bg-neutral rounded-full;
    @apply -top-7 translate-y-1/2 scale-50 left-1/2 right-1/2 -translate-x-1/2 opacity-0;
    transition-timing-function: cubic-bezier(0.63, -0.87, 0, 1.7) !important;
    @apply duration-300 transition-all;
}
</style>

<script setup>
import { ref, computed, onMounted, watch } from "vue";

const props = defineProps({
    click: {
        type: Function,
        default: () => $emit("click"),
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});
</script>
