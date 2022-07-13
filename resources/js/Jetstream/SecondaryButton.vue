<template>
    <button
        :type="type"
        class="inline-flex items-center px-4 py-2 bg-base-300 border border-base-100-300 rounded-md font-semibold text-xs uppercase tracking-widest shadow-sm focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active: active:bg-base-100 disabled:opacity-25 transition"
    >
        <slot></slot>
    </button>
</template>

<script setup>
const props = defineProps({
    type: {
        type: String,
        default: "button",
    },
});
</script>
